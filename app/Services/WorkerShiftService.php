<?php

namespace App\Services;

use App\Helpers\DailyWorkRound;
use App\Helpers\Helper;
use App\Models\ShiftManager;
use Carbon\Carbon;
use App\Validators\RepositoryValidator;

class WorkerShiftService
{
    /**
     * @var Helper
     */
    private Helper $helper;

    public function __construct(){
        $this->helper = new Helper();
    }

    /**
     * @param $request
     * @return bool[]|void
     */
    public function workerClockIn($request)
    {

        $alreadyWorked = $this->helper->workerDailyCheck($request);
        if($alreadyWorked) {
            $clockOut = $alreadyWorked->clock_out ??  Carbon::parse($alreadyWorked->clock_in)->addHours(8)->format('H:i');
            $message = "You have already clocked in {$alreadyWorked->clock_in}, your clock out is {$clockOut}";
            RepositoryValidator::dailyWorkerLimit($message);
        }

        $userCanWork = $this->helper->workWithinTimeFrame($request->user_id);
        if(!$userCanWork){
            $message = "You cannot work this time contact your manager";
            RepositoryValidator::error($message);
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
            $clockOut = Carbon::parse($alreadyWorked->created_at)->addHours(8)->format('H:i');

            if(strtotime($clockOut) <= strtotime(Carbon::now()->format('H:i'))){
                $alreadyWorked->clock_out = Carbon::now()->format('H:i');
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

    /**
     * @param $request
     * @param array $shifts
     * @return bool[]|string[]|null
     */
    public function shiftManager($request, array $shifts = []): ?array
    {
        $shifts = $this->helper->shiftAlreadyCreated($request);
        if(!$shifts){
            $shifts = $this->helper->shiftManager($request);
        }
        return $shifts;

    }

    /**
     * @param $request
     * @param array $shifts
     * @return string[]|null
     */
    public function dailyRoster($request, array $shifts = []): ?array
    {
        $shift = ['status' => 'A shift has not been set for you today. Contact your manager'];
        if(in_array(Carbon::now()->format('l'), ['Sunday', 'Saturday'])){
            $shift = ['status' => 'You are off today'];
        }
        $dailyRoster = $this->helper->startAndClosingTime($request->user_id);
        if($dailyRoster){
            $shift = ['status' => "This user shift has been created for {$this->helper->parseDate($dailyRoster->start_date)} to  {$this->helper->parseDate($dailyRoster->end_date)}"];
        }
        return $shift;
    }

}
