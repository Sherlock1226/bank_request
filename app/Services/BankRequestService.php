<?php

namespace App\Services;

use App\Models\BankAcc;
use App\Repositories\BankDetailRepository;

//use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class BankRequestService
{
    protected $bankRequest;
    protected $bankDetailRepository;


    protected $url = 'https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx';
//    protected $custId = '46399636';//企業戶ID X(10)
//    protected $custNickName = 'program01';//使用者代號
//    protected $custPwd = 'Wi63699364';//使用者密碼
//    protected $acno = '048035003676';//帳號 9(12) 048035003676 048087008676 048087009559
//    protected $fromDate;//起始時間(YYYYMMDD) 9(08)
//    protected $toDate;//結束時間(YYYYMMDD) 9(08)
//    protected $xml = 'Y';//是否回傳xml,預設Y X(01)
//    protected $txdate8 = 'Y';//是否回傳西元年,預設Y X(10)
    protected $config = [
        'url' => 'https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx',
        'custId' => '46399636',
        'custNickname' => 'program01',
        'custPwd' => 'Wi63699364',
//        'acno' => '048087009559'
    ];
    protected $bankAcc;


    public function __construct(BankDetailRepository $bankDetailRepository)
    {
        $this->bankDetailRepository = $bankDetailRepository;
    }

    public function getBankResponse(array $args)
    {

        $from_date = $args['from_date'] ?? date('Ymd', strtotime("-1 days"));
        $to_date = $args['to_date'] ?? date('Ymd', strtotime("-1 days"));
        $acno = $args['acno'] ?? '048087009559';

        $config = [
            'url' => 'https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx',
            'custId' => '46399636',
            'custNickname' => 'program01',
            'custPwd' => 'Wi63699364',
        ];

        $data = [
            'cust_id' => $config['custId'],
            'cust_nickname' => $config['custNickname'],
            'cust_pwd' => $config['custPwd'],
            'acno' => $acno,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'xml' => "Y",
            'txdate8' => "Y"
        ];
        $response = $this->request_GlobalMyB2B($config['url'], $data);


        return $response;
    }


    function request_GlobalMyB2B($url, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function argSAPData(array $bankRSdata): array
    {
        $data = [];
        foreach ($bankRSdata as $key) {
            $data[] = [
                'BANKID' => '013', //銀行代號 先寫死
                'BACCNO' => $key['BACCNO'], //銀行帳號
                'TXDATE' => $key['TX_DATE'], //交易日期
                'TXTIME' => $key['TX_TIME'], //交易時間 (hhmmssss)
                'TX_SEQNO' => $key['TX_SEQNO'], //交易序號
                'TX_IDNO' => $key['TX_IDNO'],//交易代號
                'CHKNO' => $key['CHNO'],//支票號碼
                'DC' => $key['DC'],//借貸
                'AMOUNT' => $key['AMOUNT'], //交易金額
                'BAMOUNT' => $key['BAMOUNT'], //帳戶餘額
                'MEMO1' => $key['MEMO1'],//備註一
                'MEMO2' => $key['MEMO2'],//備註二
                'XBANKID' => $key['BANKID'],//對方行
                'ACCNAME' => $key['ACCNAME'],//戶名
                'CURY' => $key['CURY'],//幣別
            ];
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function insertBankDetail(array $bankRSdata): JsonResponse
    {

        $rs = [];

        foreach ($bankRSdata as $key) {
            $amount = ($key['AMOUNT']/100);
            $bamount = ($key['BAMOUNT']/100);
            $data = [
                'BANKID' => '013', //銀行代號 先寫死
                'BACCNO' => $key['BACCNO'], //銀行帳號
                'TXDATE' => Carbon::parse((string)$key['TX_DATE'])->format('Y-m-d'), //交易日期
                'TXTIME' => $key['TX_TIME'], //交易時間 (hhmmssss)
                'TXSEQNO' => $key['TX_SEQNO'] ?? '', //交易序號
                'TXIDNO' => $key['TX_IDNO'] ?? '',//交易代號
                'CHKNO' => !empty($key['CHNO']) ? $key['CHNO'] : '',//支票號碼
                'DC' => !empty($key['DC']) ? $key['DC'] : '',//借貸
                'AMOUNT' => $amount, //交易金額
                'BAMOUNT' => $bamount, //帳戶餘額
                'MEMO1' => !empty($key['MEMO1']) ? $key['MEMO1'] : '',//備註一
                'MEMO2' => !empty($key['MEMO2']) ? $key['MEMO2'] : '',//備註二
                'XBANKID' => !empty($key['BANKID']) ? $key['BANKID'] : '',//對方行
                'ACCNAME' => !empty($key['ACCNAME']) ? $key['ACCNAME'] : '',//戶名
                'CURY' => $key['CURY'],//幣別
                'SIGN' => $key['SIGN'],//交易金額正負號
                'BSIGN' => $key['BSIGN'],//帳戶餘額正負號
                'TX_SPEC' => !empty($key['TX_SPEC']) ? $key['TX_SPEC'] : '',//交易說明
            ];
            $rs = $this->bankDetailRepository->saveData($data);
        }

        return $rs;
    }

    /**
     * @param array $args
     * @return array|Builder[]|Collection
     */
    public function getBankDetail(array $args)
    {
        $from_date = date('Y-m-d', strtotime($args['from_date'])) ?? date('Ymd', strtotime("-1 days"));
        $to_date = date('Y-m-d', strtotime($args['to_date'])) ?? date('Ymd', strtotime("-1 days"));

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'bank_acc' => $args['bank_acc']
        ];

        return $this->bankDetailRepository->getData($data);
    }


}
