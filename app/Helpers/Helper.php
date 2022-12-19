<?php

namespace App\Helpers;

use App\Models\WorkerShift;
use Carbon\Carbon;

class Helper
{

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
        $sevenDays = 60 * 60 * 24 * 7;
        $endDate = date('Y-m-d', (strtotime($request->end_date) - ((strtotime($request->end_date) - strtotime($request->start_date)) - $sevenDays)));
        return $this->getWorkerShift($request)->whereBetween('created_at', [$request->start_date, $endDate])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) use($request) {
                return Carbon::parse($date->created_at)->format(DailyWorkRound::groupFormat($request->type));
            });
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

}
