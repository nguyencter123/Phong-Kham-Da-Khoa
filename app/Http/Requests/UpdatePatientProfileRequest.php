<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePatientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'patient';
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'phone' => 'required|numeric|unique:users,phone,' . $userId,
            'citizen_id' => 'required|digits:12|unique:users,citizen_id,' . $userId,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Trường thông tin này không được để trống',
            'email.required' => 'Trường thông tin này không được để trống',
            'phone.required' => 'Trường thông tin này không được để trống',
            'citizen_id.required' => 'Trường thông tin này không được để trống',
            
            'email.unique' => 'Thông tin này đã tồn tại trên hệ thống. Vui lòng kiểm tra lại',
            'phone.unique' => 'Thông tin này đã tồn tại trên hệ thống. Vui lòng kiểm tra lại',
            'citizen_id.unique' => 'Thông tin này đã tồn tại trên hệ thống. Vui lòng kiểm tra lại',

            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp',
            
            // Other fallback messages
            'email.email' => 'Định dạng email không hợp lệ.',
            'phone.numeric' => 'Số điện thoại phải là chữ số.',
            'citizen_id.digits' => 'CCCD phải đủ 12 chữ số.',
        ];
    }
}
