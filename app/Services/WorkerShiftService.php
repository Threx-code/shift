<?php

namespace App\Services;

use App\Helpers\Helper;
use Carbon\Carbon;
use App\Validators\RepositoryValidator;


class WorkerShiftService
{
    private Helper $helper;
    public function __construct(){
        $this->helper = new Helper();
    }

    /**
     * @param $request
     * @return bool[]|null
     */
    public function workerClockIn($request): ?array
    {
        $alreadyWorked = $this->helper->workerDailyCheck($request);
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

        if($this->helper->workerClockIn($request)){
            return ['clock_in' => true];
        }
        $message = 'Unable to clock in';
        RepositoryValidator::dailyWorkerLimit($message);

    }

    /**
     * @param $request
     * @return bool[]|void
     */
    public function workerClockOut($request)
    {
        $alreadyWorked = $this->helper->workerDailyCheck($request);
        if($alreadyWorked) {
            $clockOut = Carbon::parse($alreadyWorked->clock_in)->addHours(8)->format('H:i');

            if(strtotime($clockOut) <= strtotime(Carbon::now()->format('H:i'))){
                $alreadyWorked->clock_out = $clockOut;
                $alreadyWorked->save();
                return ['clock_out' => true];
            }
            RepositoryValidator::dailyWorkerClockOut($clockOut);
        }
        $message = "This user hasn't clocked in today";
        RepositoryValidator::error($message);
    }


    /**
     * @param $request
     * @return array|int[]|mixed
     */
    public function listOfAllShiftForAWorker($request): mixed
    {
        switch (strtolower($request->type)){
            case 'daily':
                return $this->helper->dailyShift($request);
            case 'weekly':
                return $this->helper->weeklyShift($request);
            case 'monthly':
                return $this->helper->monthlyShift($request);
            case 'yearly':
                return $this->helper->yearly($request);
        }
        return [];
    }

}
