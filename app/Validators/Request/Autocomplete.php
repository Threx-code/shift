<?php

namespace App\Validators\Request;

use App\Validators\ValidatorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Autocomplete
{

    /**
     * @param Request $request
     * @return bool|mixed
     */
    public static function validate(Request $request): mixed
    {
        $validator = Validator::make(
            $request->all(), [
                'term' => ['required', 'string'],
            ]
        );

        if ($validator->fails()) {
            return ValidatorResponse::validationErrors($validator->errors());
        }
        return true;
    }
}
