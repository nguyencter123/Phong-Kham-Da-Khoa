<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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

    // lọc dữ liệu đầu vào
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'phone' => [
                'required',
                'unique:users,phone'
            ],

            'citizen_id' => [
                'required',
                'digits:12',
                'unique:users,citizen_id'
            ],

            'role' => [
                'required',
                'in:admin,doctor,receptionist'
            ],

            'password' => [
                'required',
                'min:6'
            ],

            'is_active' => [
                'required',
                'boolean'
            ],

        ];
    }
    // thông báo lỗi
    public function messages(): array
    {
        return [

            'name.required' => 'Tên không được để trống.',

            'email.required' => 'Email không được để trống.',

            'email.unique' => 'Email đã tồn tại.',

            'phone.unique' => 'Số điện thoại đã tồn tại.',

            'citizen_id.unique' => 'CCCD đã tồn tại.',

            'citizen_id.digits' => 'CCCD phải gồm đúng 12 số.',

            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',

        ];
    }
}
