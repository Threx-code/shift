<?php

namespace App\Validators;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidatorResponse
{
    /**
     * @param $error
     * @return mixed
     */
    public static function validationErrors($error): mixed
    {
        $errorResponse = response()->json([
            'error' => 'The given data was invalid',
            'message' => $error,
        ], 422);

        throw new HttpResponseException($errorResponse);
    }
}
