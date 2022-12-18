<?php

namespace App\Validators;

use Illuminate\Http\Exceptions\HttpResponseException;

class RepositoryValidator
{

    /**
     * @return mixed
     */
    public static function dataAlreadyExist($message): mixed
    {
        $errorResponse = response()->json([
            'error' => 'insertion error',
            'message' => $message,
        ], 409);

        throw new HttpResponseException($errorResponse);
    }


    /**
     * @return mixed
     */
    public static function dailyWorkerClockOut($time): mixed
    {
        $errorResponse = response()->json([
            'error' => 'Clock Out Time',
            'message' => 'Your clock out time is ' . $time,
        ], 404);

        throw new HttpResponseException($errorResponse);
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

        throw new HttpResponseException($errorResponse);
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

        throw new HttpResponseException($errorResponse);
    }

    /**
     * @param $var
     * @return mixed
     */
    public static function sanitizeString($var): mixed
    {
        return filter_var(strip_tags(stripslashes($var)), FILTER_SANITIZE_STRING);
    }


}
