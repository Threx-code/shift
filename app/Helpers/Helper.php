<?php

namespace App\Helpers;

class Helper
{

    public function shiftAlreadyStarted()
    {
        //todo
    }

    public function workerDailyCheck()
    {
       //todo
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
