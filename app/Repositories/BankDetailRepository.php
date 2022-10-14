<?php

namespace App\Repositories;

use App\Interfaces\EloquentRepositoryInterface;
use App\Models\BankDetail;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
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
     * @return void
     */
    public function saveData(array $data)
    {
        try {
            $this->create($data);
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' ' . json_encode($data));
        }
    }

    /**
     * @param array $data
     * @return array|Builder[]|Collection
     */

    public function getData(array $data)
    {
        try {
            return $this->model->query()
                ->whereDate('TXDATE', '>=', $data['from_date'])
                ->whereDate('TXDATE', '<=', $data['to_date'])
                ->get();

        } catch (Exception $e) {
            Log::error($e->getMessage() . ' ' . $data['TXSEQNO']);
            return [
                'error' => 'Cannot excecute query',
                'msg' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param array $data
     * @return array|int
     */

    public function getDataBySeq(array $data)
    {
        try {
            return $this->model->query()
                ->whereDate('TXDATE', '=', $data['TXDATE'])
                ->where('TXSEQNO', '=', $data['TXSEQNO'])
                ->where('BACCNO', '=', $data['BACCNO'])
                ->count();

        } catch (Exception $e) {
            Log::error($e->getMessage() . ' ' . $data['TXSEQNO']);
            return [
                'error' => 'Cannot excecute query',
                'msg' => $e->getMessage(),
            ];
        }
    }

}
