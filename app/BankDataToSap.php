<?php

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
    'from_date' => 20220901,
    'to_date' => 20220920,
    'xml' => "y",
    'txdate8' => "y"
];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $config['url'],
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
    echo "cURL Error #:" . $err;
}

$xml = simplexml_load_string($response);
$code = $xml->attributes();

$array = json_decode(json_encode($xml), TRUE);

if ($code['error_id'] == 0 && empty($code['error_msg'])) {
    $sap_data = $array['TXDETAIL'];
    print_r($sap_data);
    //SAP
    $LOGIN = array (                                 // Set login data to R/3
        "ASHOST"=>"dcn",                // application server host name
        "SYSNR"=>"00",                       // system number
        "CLIENT"=>"350",                     // client
        "USER"=>"paul",                   // user
        "PASSWD"=>"12345",                   // password
        "CODEPAGE"=>"1100");                 // codepage

// ----------------------------------------------------------------------------


    $rfc = saprfc_open ($LOGIN);
    if (! $rfc )
    {
        echo "RFC connection failed with error:".saprfc_error();
        exit;
    }

    $fce = saprfc_function_discover($rfc, "ZFI_PHP_UPLOAD_BANK_TRANX");
    if (! $fce )
    {
        echo "Discovering interface of function module ZZ_PHP_UPLOAD_ARFQ failed";
        exit;
    }

    saprfc_table_init ($fce,"UTAB");

    foreach ($sap_data as $key) {
        $utab = [];
        $utab = [
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
        saprfc_table_append ($fce,"UTAB", $utab);
        print_r($utab);
    }
    $rc = saprfc_call_and_receive ($fce);

    if ($rc != SAPRFC_OK)
    {
        if ($rfc == SAPRFC_EXCEPTION )
            echo ("Exception raised: ".saprfc_exception($fce));
        else
            echo ("Call error: ".saprfc_error($fce));
        exit;
    }

    $return = saprfc_export ($fce,"RETURN");

    echo "<PRE>";
    echo $return;
    echo "</PRE>";


    saprfc_function_free($fce);
    saprfc_close($rfc);
} else {
    echo $code['error_msg'];
}
