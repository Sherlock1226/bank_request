<?php

namespace App\Http\Controllers;

use App\Exports\BankDetailMultipleSheets;
use App\Models\BankAcc;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BankRequestService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class EasyTestController extends Controller
{


    /**
     * 建立一個新的控制器實例
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function Test()
    {

        return 'wwwww';
    }


}
