<?php

namespace App\Helpers;

use App\Models\ShiftManager;
use App\Models\WorkerShift;
use Carbon\Carbon;

class Helper
{
    /**
     * @var ShiftManager
     */
    private ShiftManager $shiftManager;

    public function __construct(){
        $this->shiftManager =  new ShiftManager();
    }

    /**
     * @return array|string[]
     */
    public function shiftTime($shift): array
    {
        return match (strtolower($shift)){
            'morning'   => DailyWorkRound::WORKSHIFT[1],
            'afternoon' => DailyWorkRound::WORKSHIFT[2],
            'evening'   => DailyWorkRound::WORKSHIFT[3]
        };
    }

    /**
     * @return array|string[]
     */
    public function clockInTime(): array
    {

        $clockInHour = strtotime(Carbon::now()->format('H:i'));
        foreach(DailyWorkRound::WORKSHIFT as $key => $workRound){
            if($clockInHour >= strtotime($workRound[0]) && $clockInHour <=  strtotime($workRound[1])){
                return $workRound;
            }
        }
        return [];
    }

    /**
     * @param $request
     * @return mixed
     */
    public function workerDailyCheck($request): mixed
    {
       return WorkerShift::where('user_id', $request->user_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->first();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function workerClockIn($request): mixed
    {
        $clockIn = $this->clockInTime();
        return WorkerShift::create([
            'user_id' => $request->user_id,
            'clock_in' => $clockIn[0],
        ]);
    }

    /**
     * @param $request
     * @return mixed
     */
    private function getWorkerShift($request): mixed
    {
        return WorkerShift::where('user_id', $request->user_id);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function dailyShift($request): mixed
    {
        return $this->getWorkerShift($request)->orderBy('created_at', 'DESC')->get();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function weeklyShift($request): mixed
    {
        $endDate = $this->sevenDays($request->start_date, $request->end_date, 1);
        return $this->getWorkerShift($request)->whereBetween('created_at', [$request->start_date, $endDate])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format(DailyWorkRound::groupFormat($request->type));
            });
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param int $deter
     * @return string
     */
    public function sevenDays($start_date, $end_date, int $deter = 0): string
    {
        $sevenDays = 60 * 60 * 24 * (6 + $deter);
        return date('Y-m-d', (strtotime($end_date) - ((strtotime($end_date) - strtotime($start_date)) - $sevenDays)));
    }

    /**
     * @param $request
     * @return array|int[]
     */
    public function monthlyShift($request): array
    {
        $scoreArray = $var = [];
        $i = 1;
        $data = $this->getWorkerShift($request)->whereMonth('created_at', $request->month)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format(DailyWorkRound::groupFormat($request->type));
            });

        $useKey = array_keys($data->toArray());
        foreach($useKey as $key){
            if(in_array($key, $useKey, true)){
                $var[] = $data[$key];
            }
            $scoreArray['week' . $i] = $var;
            $i++;
        }

        return array_merge(DailyWorkRound::$weekArray, $scoreArray);
    }

    /**
     * @param $request
     * @return array|int[]
     */
    public function yearly($request): array
    {
        $scoreArray = $var = [];
        $data = $this->getWorkerShift($request)->whereYear('created_at', $request->year)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format(DailyWorkRound::groupFormat($request->type));
            });

        $useKey = array_keys($data->toArray());
        foreach($useKey as $key){
            if(in_array($key, $useKey, true)){
                $var[] = $data[$key];
            }
            $scoreArray[$key] = $var;
        }

        return array_merge(DailyWorkRound::$monthArray, $scoreArray);
    }

    /**
     * @param $shift
     * @return string
     */
    public function shiftToTime($shift): string
    {
        return Carbon::parse($shift)->format('H:i:s');
    }

    /**
     * @param $date
     * @param string $format
     * @return string
     */
    public function parseDate($date, string $format = 'Y-m-d H:i:s'): string
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * @param $request
     * @return bool[]
     */
    public function shiftManager($request): array
    {
        $endDate = $this->sevenDays($request->start_date, $request->end_date);
        $shift = $this->shiftTime($request->shift);
        $this->shiftManager->user_id = $request->user_id;
        $this->shiftManager->manager_id = $request->manager_id;
        $this->shiftManager->start_date = $request->start_date . ' '. $this->shiftToTime($shift[0]);
        $this->shiftManager->end_date = $endDate . ' ' . $this->shiftToTime($shift[1]);
        $this->shiftManager->save();
        return ['shift_created' => true];
    }

    /**
     * @param $request
     * @return string[]|void
     */
    public function shiftAlreadyCreated($request)
    {
        $endDate = $this->sevenDays($request->start_date, $request->end_date);
        $shift = $this->shiftManager::where('user_id', $request->user_id)->whereBetween('start_date', [$request->start_date, $endDate])->first();
        if($shift){
            return ['status' => "This user shift has already been created for {$this->parseDate($shift->start_date)} to  {$this->parseDate($shift->end_date)}"];
        }
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function startAndClosingTime($userId): mixed
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d');

        return $this->shiftManager::where('user_id', $userId)
            ->where(function($query) use($startDate, $endDate){
                $query->whereBetween('start_date', [$startDate, $endDate])->orWhereBetween('end_date', [$startDate, $endDate]);
            })->first();

    }

    /**
     * @param $userId
     * @param bool $canWork
     * @return bool|mixed
     */
    public function workWithinTimeFrame($userId, bool $canWork = false): mixed
    {
        $currentTime = strtotime(Carbon::now()->format('H:i'));
        $userWorkTime = $this->startAndClosingTime($userId);
        if($userWorkTime) {
            $clockInHour = explode(':00', Carbon::parse($userWorkTime->start_date)->format('H:i:s'));
            $clockOutHour = explode(':00', Carbon::parse($userWorkTime->end_date)->format('H:i:s'));
            if ($currentTime >= strtotime($clockInHour[0]) && $currentTime <= strtotime($clockOutHour[0])) {
                $canWork = true;
            }
        }
        return $canWork;
    }

}
