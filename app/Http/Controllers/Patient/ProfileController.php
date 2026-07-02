<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdatePatientProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Patient;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $patient = $user->patient ?? new Patient();

        return view('patient.profile', compact('user', 'patient'));
    }

    public function update(UpdatePatientProfileRequest $request)
    {
        $user = Auth::user();

        // Cập nhật thông tin bảng users
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->citizen_id = $request->citizen_id;

        // Cập nhật mật khẩu nếu có nhập
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Cập nhật thông tin bảng patients
        Patient::updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
            ]
        );

        return redirect()->route('patient.profile')->with('success', 'Cập nhật thông tin thành công');
    }
}
