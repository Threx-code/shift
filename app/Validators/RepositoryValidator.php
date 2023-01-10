<?php

namespace App\Validators;

use Illuminate\Http\Exceptions\HttpResponseException;

class RepositoryValidator
{

    /**
     * @param $message
     * @return void
     */
    public static function dataAlreadyExist($message): void
    {
        $errorResponse = response()->json([
            'error' => 'insertion error',
            'message' => $message,
        ], 409);

        self::throw($errorResponse);
    }


    /**
     * @param $time
     * @return void
     */
    public static function dailyWorkerClockOut($time): void
    {
        $errorResponse = response()->json([
            'error' => 'Clock Out Time',
            'message' => 'Your clock out time is ' . $time,
        ], 404);

        self::throw($errorResponse);
    }


    /**
     * @param $message
     * @return void
     */
    public static function dailyWorkerLimit($message): void
    {
        $errorResponse = response()->json([
            'error' => 'Daily Work Limit Reached',
            'message' => $message,
        ], 422);

        self::throw($errorResponse);
    }


    /**
     * @param $message
     * @return void
     */
    public static function error($message): void
    {
        $errorResponse = response()->json([
            'error' => 'Something went wrong',
            'message' => $message,
        ], 422);

        self::throw($errorResponse);
    }

    /**
     * @param $var
     * @return mixed
     */
    public static function sanitizeString($var): mixed
    {
        return filter_var(strip_tags(stripslashes($var)), FILTER_SANITIZE_STRING);
    }

    /**
     * @param $error
     * @return mixed
     */
    public static function throw($error): mixed
    {
        throw new HttpResponseException($error);
    }


}
