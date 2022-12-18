<?php

namespace App\Services;

use App\Helpers\DailyWorkRound;
use App\Helpers\Helper;
use App\Models\Orders;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Validators\RepositoryValidator;


class WorkerShiftService
{
    public function shiftsAWorkerDidNotWork($request)
    {
    }

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
     * @return bool[]|null
     */
    public function workerClockIn($request): ?array
    {
        $helper = new Helper();
        $alreadyWorked = $helper->workerDailyCheck($request);
        if($alreadyWorked->clock_out){
            $message = "You have already worked between {$alreadyWorked->clock_in} and {$alreadyWorked->clock_out}";
            RepositoryValidator::DailyWorkerLimit($message);
        }
        if($alreadyWorked->clock_in){
            $clockOut = Carbon::parse($alreadyWorked->clock_in)->addHours(8);
            $message = "You have already clocked in {$alreadyWorked->clock_in}, your clock out is {$clockOut}";
            RepositoryValidator::DailyWorkerLimit($message);
        }

        if($helper->workerClockIn($request)){
            return ['clock_in' => true];
        }
        $message = 'Unable to clock in';
        RepositoryValidator::DailyWorkerLimit($message);

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
