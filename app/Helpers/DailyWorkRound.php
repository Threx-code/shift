<?php

namespace App\Helpers;

class DailyWorkRound
{

    public const WORKSHIFT = [
        1 => ['0:00', '8:00'],
        2 => ['8:00', '16:00'],
        3 => ['16:00', '24:00']
    ];

    /**
     * @var array|int[]
     */
    public static array $weekArray = ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'week5' => 0];

    /**
     * @var array|int[]
     */
    public static array $monthArray = ['Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0,];

    /**
     * @param $type
     * @return string
     */
    public static function groupFormat($type): string
    {
        return match (strtolower($type)){
            '','daily', 'weekly' => 'l',
            'monthly'            => 'W',
            'yearly'            => 'M'
        };
    }

}
