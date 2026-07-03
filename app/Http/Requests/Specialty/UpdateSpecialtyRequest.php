<?php

namespace App\Http\Requests\Admin\Specialty;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name'=>[
                'required',
                'string',
                'max:255',

                Rule::unique('specialties')
                    ->ignore($this->specialty)
            ],

            'description'=>[
                'nullable',
                'string',
                'max:1000'
            ]

        ];
    }

    public function messages(): array
    {
        return [

            'name.required'=>'Tên chuyên khoa không được để trống.',

            'name.unique'=>'Tên chuyên khoa đã tồn tại.',

        ];
    }
}