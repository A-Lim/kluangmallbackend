<?php
namespace App\Repositories\Receipt;

use App\User;
use App\Receipt;
use Carbon\Carbon;
use App\SystemSetting;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptRepository implements IReceiptRepository {

    /**
     * {@inheritdoc}
     */
    public function exists($invoice_no) {
        return Receipt::where('invoice_no', $invoice_no)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = Receipt::join('merchants', 'merchants.id', '=', 'receipts.merchant_id')
            ->select('receipts.*', 'merchants.name as merchant');

        if (isset($data['merchant']))
            $this->queryWhere($query, $data['merchant'], 'merchants.name');

        if (isset($data['date']))
            $this->queryWhere($query, $data['date'], 'receipts.date', true);

        // sort
        if (isset($data['sort'])) {
            $sortData = explode(';', $data['sort']);
            foreach($sortData as $sortDetail) {
                $sortData = explode(':', $sortDetail);
                if (count($sortData) < 2) {
                    // throw exception
                }
                $sortCol = $sortData[1];
                $sortType = $sortData[0];

                $sortKey = $this->getSortKey($sortCol);
                $query->orderBy($sortKey, $sortType);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function listMy(User $user, $data, $paginate = false) {
        $query = Receipt::join('merchants', 'merchants.id', '=', 'receipts.merchant_id')
            ->where('receipts.user_id', $user->id)
            ->select('receipts.*', 'merchants.name as merchant_name')
            ->orderBy('receipts.id', 'desc');

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    public function create($data) {
        $systemSettings = SystemSetting::where('code', 'points_rate')
            ->first();

        $points_rate = @$systemSettings->value ?? 1;

        $data['points'] = round($data['amount'], 0) * $points_rate;
        return Receipt::create($data);
    }

    private function queryWhere(Builder $query, $data, $key, $isDate = false) {
        $filterData = explode(':', $data);

        if (count($filterData) > 1 && $filterData[0] == 'contains' && $isDate)
            $query = $query->whereDate($key, 'LIKE', '%'.$filterData[1].'%');
        else if (count($filterData) > 1 && $filterData[0] == 'contains' && !$isDate)
            $query = $query->where($key, 'LIKE', '%'.$filterData[1].'%');
        else if (count($filterData) > 1 && $filterData[0] == 'equals' && $isDate)
            $query = $query->whereDate($key, $filterData[1]);
        else if (count($filterData) > 1 && $filterData[0] == 'equals' && !$isDate)
            $query = $query->where($key, $filterData[1]);
        else 
            $query = $query->where($key, $filterData[0]);

        return $query;
    }

    private function getSortKey($column) {
        switch ($column) {
            case 'merchant':
                return 'merchants.name';

            case 'date':
                return 'receipts.date';

            case 'amount':
                return 'receipts.amount';

            case 'points':
                return 'receipts.points';
            
            default:
                return null;
        }
    }
}