<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ShiftManager;
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
                $message = "You have already clocked in {$alreadyWorked->clock_in}, your clock out is {$alreadyWorked->clock_out}";
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
                if($clockOut == '00:01'){
                    $clockOut = '24:00';
                }
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
        return match (strtolower($request->type)){
            'daily' => $this->helper->dailyShift($request),
            'weekly' => $this->helper->weeklyShift($request),
            'monthly' => $this->helper->monthlyShift($request),
            'yearly' => $this->helper->yearly($request),
        };
    }

    public function shiftManager($request, $shifts = [])
    {
        $shifts = $this->helper->shiftAlreadyCreated($request);
        if(!$shifts){
            $shifts = $this->helper->shiftManager($request);
        }
        return $shifts;

    }

}
