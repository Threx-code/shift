<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\WorkerShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Validators\RepositoryValidator;


class WorkerShiftService
{
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


    public function dailyShift($workerShifts)
    {
        return $workerShifts->orderBy('created_at', 'DESC')->get();
    }

    public function weeklyShift($workerShifts, $request)
    {
        $sevenDays = 60 * 60 * 24 * 7;
        $endDate = date('Y-m-d', (strtotime($request->end_date) - ((strtotime($request->end_date) - strtotime($request->start_date)) - $sevenDays)));
        return $workerShifts->whereBetween('created_at', [$request->start_date, $endDate])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format($this->groupFormat($request->type));
            });
    }


    public function monthlyShift($workerShifts, $request)
    {
        $weekArray = ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'week5' => 0];
        $scoreArray = $var = [];
        $i = 1;

        $data = $workerShifts->whereMonth('created_at', $request->month)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format($this->groupFormat($request->type));
            });

        $useKey = array_keys($data->toArray());
        foreach($useKey as $key){
            if(in_array($key, $useKey, true)){
                $var[] = $data[$key];
            }
            $scoreArray['week' . $i] = $var;
            $i++;
        }

        return array_merge($weekArray, $scoreArray);
    }


    public function yearly($workerShifts, $request)
    {
        $monthArray = ['Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0,];
        $scoreArray = $var = [];

        $data = $workerShifts->whereYear('created_at', $request->year)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format($this->groupFormat($request->type));
            });

        $useKey = array_keys($data->toArray());
        foreach($useKey as $key){
            if(in_array($key, $useKey, true)){
                $var[] = $data[$key];
            }
            $scoreArray[$key] = $var;
        }

        return array_merge($monthArray, $scoreArray);
    }

    public function listOfAllShiftForAWorker($request)
    {
        $workerShifts = WorkerShift::where('user_id', $request->user_id);
        switch (strtolower($request->type)){
            case 'daily':
                return $this->dailyShift($workerShifts);
            case 'weekly':
                return $this->weeklyShift($workerShifts, $request);
            case 'monthly':
                return $this->monthlyShift($workerShifts, $request);
            case 'yearly':
                return $this->yearly($workerShifts, $request);
        }
    }

    /**
     * @param $type
     * @return string
     */
    public function groupFormat($type): string
    {
        return match (strtolower($type)){
           '','daily', 'weekly' => 'l',
           'monthly'=> 'W',
            'yearly' => 'M'
        };
    }

    public function shiftsAWorkerDidNotWork($request)
    {
    }

    /**
     * @param $date
     * @return string
     */
    private function filterByDate($date): string
    {
        if(!empty($date['start_date']) && !empty($date['end_date'])) {
            $orderDate = "created_at between '{$date['start_date']}' AND '{$date['end_date']}'";
        }
        return $orderDate ?? 'id IS NOT NULL';
    }

}
