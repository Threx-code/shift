<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Validators\RepositoryValidator;


class WorkerShiftService
{
    public function shiftsAWorkerDidNotWork($request)
    {
    }

    /**
     * @param $request
     * @return bool[]|null
     */
    public function workerClockIn($request): ?array
    {
        $helper = new Helper();
        $alreadyWorked = $helper->workerDailyCheck($request);
        if($alreadyWorked) {
            if ($alreadyWorked->clock_out) {
                $message = "You have already worked between {$alreadyWorked->clock_in} and {$alreadyWorked->clock_out}";
                RepositoryValidator::dailyWorkerLimit($message);
            }
            if ($alreadyWorked->clock_in) {
                $clockOut = Carbon::parse($alreadyWorked->clock_in)->addHours(8)->format('H:i');
                $message = "You have already clocked in {$alreadyWorked->clock_in}, your clock out is {$clockOut}";
                RepositoryValidator::dailyWorkerLimit($message);
            }
        }

        if($helper->workerClockIn($request)){
            return ['clock_in' => true];
        }
        $message = 'Unable to clock in';
        RepositoryValidator::dailyWorkerLimit($message);

    }

    public function workerClockOut($request)
    {
        $helper = new Helper();
        $alreadyWorked = $helper->workerDailyCheck($request);
        if($alreadyWorked) {
            $clockOut = Carbon::parse($alreadyWorked->clock_in)->addHours(8)->format('H:i');
            if(strtotime($clockOut) >= strtotime(Carbon::now()->format('H:i'))){
                $alreadyWorked->clock_out = $helper->clockInTime()[1];
                $alreadyWorked->save();
                return ['clock_out' => true];
            }
            RepositoryValidator::dailyWorkerClockOut($clockOut);
        }
        $message = "This user hasn't clocked in today";
        RepositoryValidator::error($message);
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


    public function getAllTheShifts($request)
    {

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

}
