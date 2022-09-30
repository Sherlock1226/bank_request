<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BankRequestService;



class BankRequestController extends Controller
{

    protected $bankRequestService;



    /**
     * 建立一個新的控制器實例
     *
     * @return void
     */
    public function __construct(BankRequestService $bankRequestService)
    {
        $this->bankRequestService = $bankRequestService;

    }

    public function getInfo(Request $request)
    {
        try {
//            $args = $request->all();
            $args = [
                'from_date' => 20220901,
                'to_date' => 20220921
            ];

            $response = $this->bankRequestService->getBankResponse($args);
            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $rs_array = json_decode($json,TRUE);
            print_r($rs_array);
            if($rs_array['error_id'] == 0 && empty($rs_array['error_msg'])){
                $sap_data = $this->bankRequestService->argSAPData($args);
            }else{
                print_r($rs_array);
            }

        } catch (Exception $e) {
            Log::error($e);
        }
    }

}
