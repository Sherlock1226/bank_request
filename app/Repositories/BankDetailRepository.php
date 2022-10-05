<?php

namespace App\Repositories;

use App\Interfaces\EloquentRepositoryInterface;
use App\Models\BankDetail;
use http\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankDetailRepository extends BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param BankDetail $model
     */
    public function __construct(BankDetail $model)
    {
        parent::__construct($model);
    }


    /**
     * @param array $data
     * @return JsonResponse
     * @throws \Exception
     */
    public function saveData(array $data): JsonResponse
    {
        try {

            $this->create($data);

        } catch (\Exception $e) {
            Log::error($e->getMessage().' '.$data['TXSEQNO']);
            return response()->json([
                'error' => 'Cannot excecute insert',
                'msg' => $e->getMessage(),
                'TXSEQNO' => $data['TXSEQNO'],
            ], 422);
        }

        return response()->json([
            'msg' => 'success',
        ], 200);
    }

    /**
     * @param array $data
     * @return JsonResponse
     * @throws \Exception
     */
    public function getData(array $data): JsonResponse
    {
        try {
            $rs = $this->model->query()
                ->where('BACCNO', $data['bank_acc'])
                ->whereDate('TXDATE', '>=', $data['from_date'])
                ->whereDate('TXDATE', '<=', $data['to_date'])
                ->get();

        } catch (\Exception $e) {
            Log::error($e->getMessage().' '.$data['TXSEQNO']);
            return response()->json([
                'error' => 'Cannot excecute query',
                'msg' => $e->getMessage(),
            ], 422);
        }

        return response()->json( $rs , 200);
    }

}
