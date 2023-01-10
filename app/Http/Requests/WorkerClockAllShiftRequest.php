<?php

namespace App\Http\Requests;

use App\Validators\ValidatorResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class WorkerClockAllShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $type = request()->input('type');
        return [
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
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator): void
    {
        ValidatorResponse::validationErrors($validator->errors());
    }
}
