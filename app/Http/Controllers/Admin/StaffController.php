<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['doctor', 'receptionist']);
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('citizen_id', 'like', "%{$search}%");
            });
        }

        $staffs = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.staff.index', compact('staffs'));
    }

    public function create()
    {
        $specialties = Specialty::all();
        return view('admin.staff.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:doctor,receptionist',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'citizen_id' => 'required|string|max:20',
            'specialty_id' => 'required_if:role,doctor|nullable|exists:specialties,id',
            'title' => 'required_if:role,doctor|nullable|string|max:100',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Alternative Flow E1: Check duplicates
        $duplicate = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->orWhere('citizen_id', $request->citizen_id)
            ->first();

        if ($duplicate) {
            $errors = [];
            if ($duplicate->email === $request->email) $errors['email'] = 'Email này đã tồn tại trên hệ thống.';
            if ($duplicate->phone === $request->phone) $errors['phone'] = 'SĐT này đã tồn tại trên hệ thống.';
            if ($duplicate->citizen_id === $request->citizen_id) $errors['citizen_id'] = 'CCCD này đã tồn tại trên hệ thống.';
            return back()->withErrors($errors)->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'citizen_id' => $request->citizen_id,
                'password' => Hash::make('1'),
                'role' => $request->role,
                'is_active' => true,
            ]);

            if ($request->role === 'doctor') {
                $doctorData = [
                    'user_id' => $user->id,
                    'specialty_id' => $request->specialty_id,
                    'title' => $request->title,
                    'bio' => $request->bio,
                ];
                if ($request->hasFile('avatar')) {
                    $doctorData['avatar'] = $request->file('avatar')->store('avatars', 'public');
                }
                Doctor::create($doctorData);
            }

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Thêm nhân sự mới thành công! Mật khẩu mặc định là: 1');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $staff = User::with('doctor')->findOrFail($id);
        if (!in_array($staff->role, ['doctor', 'receptionist'])) {
            return redirect()->route('admin.staff.index')->with('error', 'Chỉ có thể sửa thông tin Bác sĩ hoặc Lễ tân.');
        }
        $specialties = Specialty::all();
        return view('admin.staff.edit', compact('staff', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'citizen_id' => 'required|string|max:20',
            'specialty_id' => 'required_if:role,doctor|nullable|exists:specialties,id',
            'title' => 'required_if:role,doctor|nullable|string|max:100',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // E1: Check duplicates ignoring self
        $duplicate = User::where('id', '!=', $id)
            ->where(function($q) use ($request) {
                $q->where('email', $request->email)
                  ->orWhere('phone', $request->phone)
                  ->orWhere('citizen_id', $request->citizen_id);
            })->first();

        if ($duplicate) {
            $errors = [];
            if ($duplicate->email === $request->email) $errors['email'] = 'Email này đã tồn tại trên hệ thống.';
            if ($duplicate->phone === $request->phone) $errors['phone'] = 'SĐT này đã tồn tại trên hệ thống.';
            if ($duplicate->citizen_id === $request->citizen_id) $errors['citizen_id'] = 'CCCD này đã tồn tại trên hệ thống.';
            return back()->withErrors($errors)->withInput();
        }

        try {
            DB::beginTransaction();

            $staff->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'citizen_id' => $request->citizen_id,
            ]);

            if ($staff->role === 'doctor') {
                $doctorData = [
                    'specialty_id' => $request->specialty_id,
                    'title' => $request->title,
                    'bio' => $request->bio,
                ];
                
                $doctor = $staff->doctor;
                if ($request->hasFile('avatar')) {
                    if ($doctor && $doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
                        Storage::disk('public')->delete($doctor->avatar);
                    }
                    $doctorData['avatar'] = $request->file('avatar')->store('avatars', 'public');
                }

                if ($doctor) {
                    $doctor->update($doctorData);
                } else {
                    $doctorData['user_id'] = $staff->id;
                    Doctor::create($doctorData);
                }
            }

            DB::commit();
            return redirect()->route('admin.staff.index')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function toggleActive($id)
    {
        $staff = User::findOrFail($id);
        
        // E2: Admin vô tình khóa chính mình
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }
        
        if (!in_array($staff->role, ['doctor', 'receptionist'])) {
             return back()->with('error', 'Bạn chỉ có thể khóa tài khoản Bác sĩ hoặc Lễ tân.');
        }

        $staff->is_active = !$staff->is_active;
        $staff->save();

        $action = $staff->is_active ? 'Mở khóa' : 'Khóa';
        return back()->with('success', "Đã {$action} tài khoản thành công!");
    }

    public function resetPassword($id)
    {
        $staff = User::findOrFail($id);
        
        if (!in_array($staff->role, ['doctor', 'receptionist'])) {
             return back()->with('error', 'Bạn chỉ có thể thao tác với Bác sĩ hoặc Lễ tân.');
        }

        $staff->password = Hash::make('1');
        $staff->save();

        return back()->with('success', 'Đã cấp lại mật khẩu mặc định (1) thành công! Hãy thông báo cho nhân viên.');
    }
}
