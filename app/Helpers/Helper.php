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
     * @return array|string[]
     */
    public function shiftAlreadyStarted(): array
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
     * @param $referredDistributors
     * @param $price
     * @param $categoryId
     * @return array|int[]
     */
    public function getDistributorPercentage($referredDistributors, $price, $categoryId): array
    {
        if($categoryId == 2){
            return [
                'percentage' => 0,
                'commission' => 0
            ];
        }
        $referredDistributors = trim($referredDistributors);

        switch ($referredDistributors){
            case ($referredDistributors  === 0 ):
                $percentage = 5;
                break;
            case ($referredDistributors  >= 5 && $referredDistributors <= 10 ):
                $percentage = 10;
                break;
            case ($referredDistributors  >= 11 && $referredDistributors <= 20 ):
                $percentage = 15;
                break;
            case ($referredDistributors  >= 21 && $referredDistributors <= 30 ):
                $percentage = 20;
                break;
            case ($referredDistributors > 30):
                $percentage = 30;
                break;
            default:
                $percentage = 5;
        };

        return [
            'percentage' => $percentage,
            'commission' =>  round(($percentage  / 100) * $price , 2)
        ];
    }
}
