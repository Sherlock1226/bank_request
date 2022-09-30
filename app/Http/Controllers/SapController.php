<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SapService;


class SapController extends Controller
{

    protected $sapService;


    /**
     * 建立一個新的控制器實例
     *
     * @return void
     */
    public function __construct(SapService $sapService)
    {
        $this->sapService = $sapService;

    }

    public function loginSap()
    {
        phpinfo();exit();
        try {
            //登入資訊
            $login = $this->sapService->sapLogin();
            if ($login['status'] != 200) {
                return $login['msg'];
            }

        } catch (Exception $e) {
            Log::error($e);
        }
    }

}
