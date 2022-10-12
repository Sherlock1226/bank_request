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
            $date = $request->input('dateFrom');
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

    public function callBank(array $data)
    {
        try {
//            $args = $request->all();

            $args = [
                'from_date' => 20220901,
                'to_date' => 20221003,
                'acno' => $data['acno']
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

    }


    public function getDetail($args)
    {
        $response = [];
        try {

            $response = $this->bankRequestService->getBankDetail($args);
        } catch (Exception $e) {
            Log::error($e);

        }
        return $response;

    }

    public function export(Request $request)
    {
        try {
            $args = $request->all();

            $validator = Validator::make($request->all(),[
                'from_date' => 'required',
                'to_date' => 'required|after_or_equal:from_date',
            ]);

            if($validator->fails()) {
                return redirect('/bankdetailexport')
                    ->withErrors($validator)
                    ->withInput();
            }
            $bd = $this->getDetail($args);

            $array = json_decode(json_encode($bd), true);
            if(empty($array)){
                return '無交易紀錄';
            }
            $da = $cur =[];
            foreach ($array as $k ) {
                $da[$k['BACCNO']][] = $k['CURY'];
            }
            foreach ($da as $k => $v){
                $cur[$k] = array_unique($v);    //去重：去掉重复的字符串
            }

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

    /**
     * @return BankAcc[]|array|Collection
     */
    public function getBankAccByAccNum()
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
