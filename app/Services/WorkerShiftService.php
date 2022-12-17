<?php

namespace App\Services;

use App\Helpers\KeywordProcessingHelper;
use App\Helpers\Helper;
use App\Models\Orders;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Validators\RepositoryValidator;
use SM\Backend\KeywordList\Managers\ListManager;
use SM\Backend\Topics\Managers\BackendManager;

class WorkerShiftService
{

    /**
     * @param $request
     * @return LengthAwarePaginator
     */
    private function workerClockIn2($request): LengthAwarePaginator
    {
        $date = ['from' => $request->from_date, 'to' => $request->to_date];
        $whereClause = $this->filterByDate($date);

        return Orders::with([
            'userCategory',
            'user',
            'orderItem' => function($query){
                $query->with('product');
            }])
            ->whereRaw($whereClause)
            ->paginate(20);
    }

    /**
     * @param $request
     * @return array
     */
    public function workerClockIn($request): array
    {
        $data = [];
        $orders = $this->allOrders($request);
        foreach($orders as $key => $order){
            $distributor = $this->getDistributor($order->user->referred_by ?? null);
            $data[$key]['invoice'] = $order->invoice_number;
            $data[$key]['purchaser'] = $order->user->first_name . ' ' . $order->user->last_name;
            $data[$key]['distributor'] = '';
            if(!empty(json_decode($distributor, true))){
                $data[$key]['distributor'] = $distributor[0]->first_name  . ' '  . $distributor[0]->last_name;
            }
            $data[$key]['referred_distributors'] = 0;
            if(!empty(json_decode($distributor, true))){
                $data[$key]['referred_distributors'] = $this->referredDistributors($distributor[0]->id, $order->order_date);
            }
            $data[$key]['order_date'] = $order->order_date;
            $data[$key]['total'] = array_sum(array_column(array_column(json_decode($order->orderItem), 'product'), 'price')) * array_sum(array_column(json_decode($order->orderItem), 'qantity'));
            $distributorCommissions = (new Helper())->getDistributorPercentage($data[$key]['referred_distributors'], $data[$key]['total'], $order->userCategory->category_id);
            $data[$key]['percentage'] = $distributorCommissions['percentage'] . "%";
            $data[$key]['commission'] = $distributorCommissions['commission'];
            $data[$key]['orderItem'] = $order->orderItem;

        }

        return [
            'data' => $data,
            'orders' => $orders
            ];
    }

    /**
     * @param $referredBy
     * @return mixed
     */
    public function workerClockOut($referredBy): mixed
    {
        return User::whereExists(function($query) use($referredBy){
            $query->from('user_category')->where('category_id', 1)
                ->whereColumn('users.id', 'user_category.user_id');
        })->where('id', $referredBy)->get();
    }

    /**
     * @param $referredBy
     * @param $dateReferred
     * @return mixed
     */
    private function referredDistributors($referredBy, $dateReferred): mixed
    {
        return User::where('referred_by', $referredBy)->whereDate('enrolled_date', '<=', date($dateReferred))->count();
    }

    /**
     * @param $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTheShifts($request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = '
        WITH
        distributors as (
                            select users.id, users.first_name, users.last_name from users where users.id IN
                            (
                                select  user_category.user_id from user_category where user_category.category_id = 1
                            )
                        ),
        customer as (
                        select
                            users.referred_by,
                            sum(order_items.qantity) as quantity,
                            sum(products.price) as productPrice
                            from orders
                            inner join users on users.id = orders.purchaser_id
                            inner join order_items on order_items.order_id = orders.id
                            inner join products on order_items.product_id = products.id
                        group by referred_by
                )
        select
            CONCAT(first_name, \' \', last_name) as name, (productPrice * quantity) as total  from
            (
                select * from distributors join customer on distributors.id = customer.referred_by
            ) as data
            order by total desc limit 100
        ';
        $collect = collect(DB::select(DB::raw($query)));
        $page = $request->page ?? 1;
        $perPage = 10;
        return new \Illuminate\Pagination\LengthAwarePaginator($collect->forPage($page, $perPage), $collect->count(), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
    }

    /**
     * @param $date
     * @return string
     */
    private function filterByDate($date): string
    {
        if(!empty($date['from']) && !empty($date['to'])) {
            $orderDate = "order_date between '{$date['from']}' AND '{$date['to']}'";
        }

        return $orderDate ?? 'id IS NOT NULL';
    }

    /**
     * @param $request
     * @return array
     */
    public function autocomplete($request): array
    {
        $data = [];
        $distributors = '%' . $request->term . '%';
        $distributors = User::select('first_name', 'last_name')
            ->where('first_name', 'Like', $distributors)
            ->orWhere('last_name', 'Like', $distributors)
            ->get();

        foreach($distributors as $distributor){
            $data[] = $distributor->first_name . ' ' . $distributor->last_name;
        }
        return $data;
    }
}
