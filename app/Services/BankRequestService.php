<?php

namespace App\Services;

use Exception;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class BankRequestService
{
    protected $bankRequest;


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
        'acno' => '048087009559'
    ];


    public function __construct()
    {

    }

    public function getBankResponse(array $args)
    {

        $from_date = $args['from_date'] ?? date('Ymd', strtotime("-1 days"));
        $to_date = $args['to_date'] ?? date('Ymd', strtotime("-1 days"));

        $config = [
            'url' => 'https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx',
            'custId' => '46399636',
            'custNickname' => 'program01',
            'custPwd' => 'Wi63699364',
            'acno' => '048087009559'
        ];

        $data = [
            'cust_id' => $config['custId'],
            'cust_nickname' => $config['custNickname'],
            'cust_pwd' => $config['custPwd'],
            'acno' => $config['acno'],
            'from_date' => $from_date,
            'to_date' => $to_date ,
            'xml' => "y",
            'txdate8' => "y"
        ];

        $response = $this->request_GlobalMyB2B($config['url'],$data);


        return $response;
    }


    function request_GlobalMyB2B($url,$data)
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
    public function argSAPData(array $args)
    {

//        foreach ($args as $key){
//
//        }
//
//        $data = [
//            'cust_id' => $config['custId'],
//            'cust_nickname' => $config['custNickname'],
//            'cust_pwd' => $config['custPwd'],
//            'acno' => $config['acno'],
//            'from_date' => $from_date,
//            'to_date' => $to_date ,
//            'xml' => "y",
//            'txdate8' => "y"
//        ];
//
//
//        return $data;
    }
}