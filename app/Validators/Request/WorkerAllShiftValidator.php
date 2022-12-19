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
        $type = $request->type;
        $validator = Validator::make(
            $request->all(), [
                'user_id' => ['required', 'integer'],
                'type' => ['required',  Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
                'start_date' => ['nullable', new RequiredIf(function () use($type) {
                    return $type == 'weekly';
                }), 'before:end_date', 'date'],
                'end_date' => ['nullable', new RequiredIf(function () use($type) {
                    return $type == 'weekly';
                }), 'after:start_date', 'date'],
                'month' => ['nullable', new RequiredIf(function () use ($type) {
                    return $type == 'monthly';
                }), 'integer'],
                'year' => ['nullable', new RequiredIf(function () use ($type) {
                    return $type == 'yearly';
                }), 'integer']
            ]
        );

        if ($validator->fails()) {
            return ValidatorResponse::validationErrors($validator->errors());
        }
        return true;
    }

}
