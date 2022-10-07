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
            $args = $request->all();

            $args = [
                'from_date' => 20220901,
                'to_date' => 20220921,
                'acno' => $args['BACCNO']
            ];

            $response = $this->bankRequestService->getBankResponse($args);
            $xml = simplexml_load_string($response);
            $code = $xml->attributes();

            $array = json_decode(json_encode($xml), TRUE);


            if ($code['error_id'] != 0 && !empty($code['error_msg'])) {
                throw new Exception($code['error_msg']);
            }

            $sap_data = $this->bankRequestService->argSAPData($array['TXDETAIL']);

            print_r($sap_data);


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
                'to_date' => 20221003,
            ];


            $response = $this->bankRequestService->getBankResponse($args);
            $xml = simplexml_load_string($response);
            $code = $xml->attributes();

            $array = json_decode(json_encode($xml), TRUE);

            if ($code['error_id'] == 0 && empty($code['error_msg'])) {

                $this->bankRequestService->insertBankDetail($array['TXDETAIL']);

            } else {
                echo $code['error_msg'];
            }

        } catch (Exception $e) {
            Log::error($e);
        }

        return response()->json(['success'], 200);

    }

//    public function callBank(Request $request): JsonResponse
//    {
//        try {
//
////            $args = $request->all();
//
//            $accno = $this->getBankAcc();
//
//            foreach ($accno as $k) {
//                $args = [
//                    'from_date' => 20220901,
//                    'to_date' => 20221006,
//                    'acno' => (string)$k['BACCNO']
//                ];
//
//                print_r($args);
//                $response = $this->bankRequestService->getBankResponse($args);
//                $xml = simplexml_load_string($response);
//                $code = $xml->attributes();
//
//                $array = json_decode(json_encode($xml), TRUE);
//
//                if ($code['error_id'] != 0 && !empty($code['error_msg'])) {
//                    Log::warning(json_encode($args) . $code['error_msg']);
//                }
//
//                $rs_db = $this->bankRequestService->insertBankDetail($array['TXDETAIL']);
//                sleep(180);
//            }
//
//        } catch (Exception $e) {
//            Log::error($e);
//            return response()->json([
//                'error' => 'Cannot excecute',
//                'msg' => $e->getMessage(),
//            ], 422);
//        }
//
//        return response()->json(['success'], 200);
//
//    }

    public function getDetail($args)
    {
        $response = [];
        try {
//            $args = $request->all();

            $args = [
                'from_date' => 20220906,
                'to_date' => 20221006,
                'bank_acc' => '048087009559'
            ];

            $response = $this->bankRequestService->getBankDetail($args);
        } catch (Exception $e) {
            Log::error($e);

        }
        return $response;

    }

    public function export()
    {
        try {
            //$args = $request->all();

            $args = [
                'from_date' => 20220906,
                'to_date' => 20220920,
                'bank_acc' => '048087009559'
            ];

            $bd = $this->getDetail($args);
            $array = json_decode(json_encode($bd), true);

            $cur = array_column($array,'CURY','CURY');
            return Excel::download(new BankDetailMultipleSheets($args, $cur), 'bank.xlsx');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            dd($e);
        }
    }


    /**
     * @return BankAcc[]|array|Collection
     */
    public function getBankAcc()
    {
        $rs = [];
        try {
            $bankAcc = new BankAcc;
            $rs = $bankAcc->all();

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $rs;
    }


}
