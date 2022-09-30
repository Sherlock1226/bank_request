<?php

//
//protected $url = 'https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx';
//protected $custId = '46399636';//企業戶ID X(10)
//protected $custNickName = 'program01';//使用者代號
//protected $custPwd = 'Wi63699364';//使用者密碼
//protected $acno = '048035003676';//帳號 9(12)
//protected $fromDate;//起始時間(YYYYMMDD) 9(08)
//protected $toDate;//結束時間(YYYYMMDD) 9(08)
//protected $xml = 'Y';//是否回傳xml,預設Y X(01)
//protected $txdate8 = 'Y';//是否回傳西元年,預設Y X(10)
return [
    'url' => env('https://www.GlobalMyB2B.com/securities/TX10F0_TXT.aspx'),
    'custId' => env('46399636'),//企業戶ID X(10)
    'custNickName' => env('program01'),//使用者代號
    'custPwd' => env('Wi63699364'),//使用者密碼
    'acno' => env('048035003676'),//帳號 9(12)

];
