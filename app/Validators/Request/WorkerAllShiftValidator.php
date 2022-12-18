<?php

namespace App\Validators\Request;

use App\Validators\ValidatorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class WorkerAllShiftValidator
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
                'start_date' => ['nullable', 'required_if:type,weekly', new RequiredIf(function () {
                    return \request()->input('end_date') != null;
                }), 'date'],
                'end_date' => ['nullable', 'after:start_date', 'date'],
                'type' => ['nullable',  Rule::in(['daily', 'weekly', 'monthly', 'yearly'])]
            ]
        );

        if ($validator->fails()) {
            return ValidatorResponse::validationErrors($validator->errors());
        }
        return true;
    }

}
