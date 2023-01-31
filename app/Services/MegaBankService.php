<?php

namespace App\Services;

use App\Repositories\BankDetailRepository;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Carbon\Carbon;


class MegaBankService
{
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

    }


    public function request_FTP()
    {
        $conf = [ 'host' => '59.120.8.31', 'port' => '22', 'user' => 'megabank', 'password' => 'S129Wi*-'];
        $conn = ssh2_connect($conf['host'], $conf['port']);
        if (!ssh2_auth_password($conn, $conf['user'], $conf['password'])) {
            var_dump('sftps 連線失敗');
        }

        return $conn;
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
     * @param array $bankRSdata
     * @return void
     */
    public function insertBankDetail(array $bankRSdata)
    {

        $cury = [
            '01' => 'TWD',
            '02' => 'USD',
            '03' => 'HKD',
            '05' => 'GBP',
            '07' => 'AUD',
            '11' => 'CAD',
            '29' => 'CNY',
            '30' => 'EUR',

        ];
        //回來只有1筆格式跟多筆不一樣
        if (!isset($bankRSdata[1])) {
            $bankDetail = $bankRSdata;
            unset($bankRSdata);
            $bankRSdata[0] = $bankDetail;
        }

        foreach ($bankRSdata as $key) {
            //先查有沒有,沒有才新增
            $qu_data = [
                'TXDATE' => Carbon::parse((string)$key['TX_DATE'])->format('Y-m-d'),
                'TXSEQNO' => $key['TX_SEQNO'],
                'BACCNO' => $key['BACCNO']
            ];
            $rs = $this->getBankDetailBySeq($qu_data);
            if ($rs < 1) {
                $amount = (intval($key['AMOUNT']) / 100);
                $bamount = (intval($key['BAMOUNT']) / 100);

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
                    'CURY' => $cury[$key['CURY']] ?? $key['CURY'],//幣別
                    'SIGN' => $key['SIGN'],//交易金額正負號
                    'BSIGN' => $key['BSIGN'],//帳戶餘額正負號
                    'TX_SPEC' => !empty($key['TX_SPEC']) ? $key['TX_SPEC'] : '',//交易說明
                ];
                $this->bankDetailRepository->saveData($data);
            }

        }
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
        ];

        return $this->bankDetailRepository->getData($data);
    }

    /**
     * @param array $args
     * @return array|int
     */
    public function getBankDetailBySeq(array $args)
    {

        $data = [
            'TXDATE' => $args['TXDATE'],
            'TXSEQNO' => $args['TXSEQNO'],
            'BACCNO' => $args['BACCNO']
        ];
        return $this->bankDetailRepository->getDataBySeq($data);
    }


}
