<?php

namespace App\Http\Requests\Admin\Specialty;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialtyRequest extends FormRequest
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
                'unique:specialties,name'
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