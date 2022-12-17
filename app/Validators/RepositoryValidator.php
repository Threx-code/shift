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
    public static function dataDoesNotExist(): mixed
    {
        $errorResponse = response()->json([
            'error' => 'Data Not Found',
            'message' => 'The given data does not exist',
        ], 404);

        throw new HttpResponseException($errorResponse);
    }


    /**
     * @param $message
     * @return void
     */
    public static function DailyWorkerLimit($message): void
    {
        $errorResponse = response()->json([
            'error' => 'Daily Work Limit Reached',
            'message' => $message . 'You have reached your daily limit of 100k keywords.',
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
