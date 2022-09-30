<?php

namespace App\Services;

use Exception;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class SapService
{
    protected $bankRequest;


    public $LOGIN = array(
        "ASHOST" => "dcn",
        "SYSNR" => "00",
        "CLIENT" => "350",
        "USER" => "paul",
        "PASSWD" => "12345",
        "CODEPAGE" => "1100");


    public function __construct($LOGIN)
    {
        $this->LOGIN = $LOGIN;
    }

    public function sapLogin()
    {
        $rfc = saprfc_open($this->LOGIN);
        if (! $rfc )
        {
            return ['status' => 501, 'msg' => "RFC connection failed with error:".saprfc_error()];
        }
    }


}
