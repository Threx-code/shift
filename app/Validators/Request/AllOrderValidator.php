<?php

namespace App\Validators\Request;

use App\Validators\ValidatorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AllOrderValidator
{

    /**
     * @param Request $request
     * @return bool|mixed
     */
    public static function validate(Request $request): mixed
    {
        $validator = Validator::make(
            $request->all(), [
                'from_date' => ['nullable', 'date', 'before:to_date'],
                 'to_date' => ['nullable', 'date', 'after:from_date']
            ]
        );

        if ($validator->fails()) {
            return ValidatorResponse::validationErrors($validator->errors());
        }
        return true;
    }

}
