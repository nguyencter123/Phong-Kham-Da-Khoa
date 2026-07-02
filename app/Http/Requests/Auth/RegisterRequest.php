<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'citizen_id' => 'required|digits:12|unique:users,citizen_id',
            'password' => 'required|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng không để trống thông tin này',
            'email.required' => 'Vui lòng không để trống thông tin này',
            'phone.required' => 'Vui lòng không để trống thông tin này',
            'citizen_id.required' => 'Vui lòng không để trống thông tin này',
            'password.required' => 'Vui lòng không để trống thông tin này',
            
            'email.email' => 'Định dạng Email không hợp lệ',
            'phone.numeric' => 'Định dạng Số điện thoại không hợp lệ',
            'citizen_id.digits' => 'Định dạng Số căn cước không hợp lệ',
            
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp',

            'email.unique' => 'Email này đã được sử dụng.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký cho một hồ sơ khác.',
            'citizen_id.unique' => 'Số căn cước này đã tồn tại trong hệ thống. Nếu đây là bạn, vui lòng liên hệ quầy tiếp tân để được hỗ trợ cấp lại mật khẩu.',
        ];
    }
}
