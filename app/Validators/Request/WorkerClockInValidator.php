<?php

namespace App\Validators\Request;

use App\Validators\ValidatorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkerClockInValidator
{
    /**
     * @param Request $request
     * @return bool|mixed
     */
    public static function validate(Request $request): mixed
    {
        $validator = Validator::make(
            $request->all(), [
                'user_id' => ['required', 'integer'],
            ]
        );

        if ($validator->fails()) {
            return ValidatorResponse::validationErrors($validator->errors());
        }
        return true;
    }

}
