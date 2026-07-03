<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
    //     return [
    //         //
    //     ];
    // }

    // cập nhật thông tin (validate)
    public function rules(): array
    {
        $user = $this->route('user');

        return [

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [

                'required',

                'email',

                Rule::unique('users')->ignore($user)

            ],

            'phone' => [

                'required',

                Rule::unique('users')->ignore($user)

            ],

            'citizen_id' => [

                'required',

                'digits:12',

                Rule::unique('users')->ignore($user)

            ],

            'role' => [

                'required',

                'in:admin,doctor,receptionist'

            ],

            'is_active' => [

                'required',

                'boolean'

            ],

        ];
    }
}
