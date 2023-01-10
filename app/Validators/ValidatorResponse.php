<?php

namespace App\Validators;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidatorResponse
{
    /**
     * @param $error
     * @return void
     */
    public static function validationErrors($error): void
    {
        $errorResponse = response()->json([
            'error' => 'The given data was invalid',
            'message' => $error->first(),
        ], 422);

        RepositoryValidator::throw($errorResponse);
    }
}
