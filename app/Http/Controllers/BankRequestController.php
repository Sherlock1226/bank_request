<?php

namespace App\Http\Controllers;

use App\Exports\BankDetailExport;
use App\Models\BankDetail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BankRequestService;
use Maatwebsite\Excel\Facades\Excel;


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
            $code = $xml->attributes();

            $array = json_decode(json_encode($xml), TRUE);

            if ($code['error_id'] == 0 && empty($code['error_msg'])) {
                $sap_data = $this->bankRequestService->argSAPData($array['TXDETAIL']);

                print_r($sap_data);
            } else {
                echo $code['error_msg'];
            }

        } catch (Exception $e) {
            Log::error($e);
        }

//        return response()->json($this->userRepository->all(), 200);

    }


    public function callBank(Request $request): JsonResponse
    {
        try {
//            $args = $request->all();

            $args = [
                'from_date' => 20220901,
                'to_date' => 20221003
            ];

            $response = $this->bankRequestService->getBankResponse($args);
            $xml = simplexml_load_string($response);
            $code = $xml->attributes();

            $array = json_decode(json_encode($xml), TRUE);

            if ($code['error_id'] == 0 && empty($code['error_msg'])) {
//                $sap_data = $this->bankRequestService->argSAPData($array['TXDETAIL']);
                $rs_db = $this->bankRequestService->insertBankDetail($array['TXDETAIL']);

            } else {
                echo $code['error_msg'];
            }

        } catch (Exception $e) {
            Log::error($e);
        }

        return response()->json(['success'], 200);

    }

    public function getExcel()
    {
        $response = [];
        try {
//            $args = $request->all();

            $args = [
                'from_date' => 20220901,
                'to_date' => 20220921
            ];

            $response = $this->bankRequestService->getBankDetail($args);



        } catch (Exception $e) {
            Log::error($e);
        }
        return $response;

    }

    public function export()
    {
        return Excel::download(new BankDetailExport($this->getExcel()), 'bank.xlsx');
    }

}
