<?php

namespace App\Services;

use Exception;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class SapService
{
    protected $bankRequest;


    public function __construct()
    {

    }

    public function sapLogin()
    {
        $LOGIN = array(
            "ASHOST" => "dcn",
            "SYSNR" => "00",
            "CLIENT" => "350",
            "USER" => "paul",
            "PASSWD" => "12345",
            "CODEPAGE" => "1100");
        $rfc = saprfc_open($LOGIN);
        if (!$rfc) {
            return ['status' => 502, 'msg' => "RFC connection failed with error:" . saprfc_error()];
        }
    }


}
