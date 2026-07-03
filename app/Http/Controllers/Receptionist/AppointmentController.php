<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function create()
    {
        $specialties = Specialty::all();
        return view('receptionist.appointments.create', compact('specialties'));
    }

    public function searchPatient(Request $request)
    {
        $term = $request->get('term');
        if (!$term) return response()->json(['found' => false]);

        $user = User::where('role', 'patient')
            ->where(function($q) use ($term) {
                $q->where('phone', $term)
                  ->orWhere('citizen_id', $term);
            })->first();

        if ($user) {
            return response()->json([
                'found' => true,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'citizen_id' => $user->citizen_id,
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function getAvailableDoctors(Request $request)
    {
        $specialtyId = $request->get('specialty_id');
        $date = Carbon::parse($request->get('date'));
        $shift = $request->get('shift');

        if (!$specialtyId || !$date || !$shift) {
            return response()->json([]);
        }

        $dayOfWeek = $date->dayOfWeek;

        $doctors = Doctor::with('user')
            ->where('specialty_id', $specialtyId)
            ->whereHas('schedules', function($q) use ($dayOfWeek, $shift) {
                $q->where('day_of_week', $dayOfWeek)
                  ->where('shift', $shift);
            })
            ->get();

        return response()->json($doctors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'citizen_id' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'shift' => 'required|in:morning,afternoon',
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Tìm hoặc tạo bệnh nhân
            $user = User::where('phone', $request->phone)
                ->orWhere('citizen_id', $request->citizen_id)
                ->orWhere('email', $request->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'citizen_id' => $request->citizen_id,
                    'password' => Hash::make($request->phone), // Default pass is phone
                    'role' => 'patient',
                ]);
                
                Patient::create([
                    'user_id' => $user->id,
                ]);
            } else {
                // Đã tìm thấy user dựa trên SĐT, CCCD, hoặc Email
                if ($user->phone !== $request->phone) {
                    // SĐT không khớp, nghĩa là CCCD hoặc Email đã bị trùng với một người khác!
                    $errors = [];
                    if ($user->citizen_id === $request->citizen_id) {
                        $errors['citizen_id'] = 'CCCD này đã được sử dụng cho một người bệnh khác.';
                    }
                    if ($user->email === $request->email) {
                        $errors['email'] = 'Email này đã được sử dụng cho một người bệnh khác.';
                    }
                    // Nếu vì lý do nào đó không check được chính xác, báo lỗi chung vào phone
                    if (empty($errors)) {
                         $errors['phone'] = 'Thông tin của bạn bị trùng lặp với người khác trên hệ thống.';
                    }
                    DB::rollBack();
                    return back()->withErrors($errors)->withInput();
                } else if ($user->citizen_id !== $request->citizen_id || $user->email !== $request->email) {
                    // SĐT khớp, nhưng CCCD hoặc Email không khớp -> Cố tình sửa thông tin của khách cũ
                    $errors = [];
                    $errors['phone'] = 'SĐT này đã tồn tại nhưng CCCD/Email không khớp với hồ sơ cũ.';
                    DB::rollBack();
                    return back()->withErrors($errors)->withInput();
                }
            }

            // 2. Kiểm tra sức chứa (Quá tải) & Tình trạng Khóa ca
            $date = Carbon::parse($request->date);
            $schedule = Schedule::where('doctor_id', $request->doctor_id)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('shift', $request->shift)
                ->where('is_active', true)
                ->first();

            if (!$schedule) {
                return back()->with('error', 'Bác sĩ không có lịch trực hoặc đã NGỪNG NHẬN BỆNH NHÂN cho ca này.')->withInput();
            }

            $currentBookings = Appointment::where('doctor_id', $request->doctor_id)
                ->whereDate('date', $date->toDateString())
                ->where('shift', $request->shift)
                ->where('status', '<', 6)
                ->count();

            if ($currentBookings >= $schedule->max_patients_per_slot) {
                return back()->with('error', 'Bác sĩ này đã kín lịch trong ca hiện tại.')->withInput();
            }

            // 3. Tạo lịch hẹn (status = 2: đang chờ khám vì đến trực tiếp)
            Appointment::create([
                'patient_id' => $user->id,
                'doctor_id' => $request->doctor_id,
                'date' => $date->toDateString(),
                'shift' => $request->shift,
                'type' => 'offline',
                'reason' => $request->reason,
                'status' => 2, 
            ]);

            DB::commit();
            return redirect()->route('receptionist.appointments.index')->with('success', 'Đã tạo lịch khám thành công. Bệnh nhân đang ở trạng thái chờ khám.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Appointment::with(['patient', 'doctor.user', 'doctor.specialty'])
            ->whereDate('date', Carbon::today())
            ->orderBy('is_priority', 'desc')
            ->orderBy('shift', 'asc');
            
        if ($search) {
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $appointments = $query->get();
        $doctors = Doctor::with(['user', 'specialty', 'schedules'])->get();

        return view('receptionist.appointments.index', compact('appointments', 'doctors', 'search'));
    }

    public function checkIn($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->doctor_id == null) {
            return back()->with('error', 'Ca khám chưa được phân công bác sĩ!');
        }

        $appointment->update(['status' => 2]); // 2: Đang chờ khám

        return back()->with('success', 'Đã Check-in thành công. Vui lòng hướng dẫn bệnh nhân đến phòng khám.');
    }

    public function assignDoctor(Request $request, $id)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'doctor_id' => $request->doctor_id,
            'status' => 2, // Đang chờ khám
        ]);

        return back()->with('success', 'Đã phân công bác sĩ và đưa vào hàng chờ thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'shift' => 'required|in:morning,afternoon',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $appointment = Appointment::findOrFail($id);
        $date = Carbon::parse($request->date);
        $doctorId = $request->doctor_id ?: $appointment->doctor_id;

        if ($doctorId) {
            $hasSchedule = Schedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('shift', $request->shift)
                ->exists();

            if (!$hasSchedule) {
                return back()->with('error', 'Bác sĩ không có lịch trực vào thời gian này.');
            }

            $schedule = Schedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('shift', $request->shift)
                ->first();

            $currentBookings = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date->toDateString())
                ->where('shift', $request->shift)
                ->where('status', '<', 6)
                ->where('id', '!=', $id)
                ->count();

            if ($schedule && $currentBookings >= $schedule->max_patients_per_slot) {
                return back()->with('error', 'Khung giờ này của bác sĩ đã đầy.');
            }
        }

        $appointment->update([
            'date' => $date->toDateString(),
            'shift' => $request->shift,
            'doctor_id' => $doctorId,
            'status' => 1, // Đã duyệt
            'is_priority' => true,
        ]);

        return back()->with('success', 'Đã dời lịch và ưu tiên ca khám thành công.');
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $appointment = Appointment::findOrFail($id);

        // Chỉ cho phép hủy nếu chưa bắt đầu khám (status < 3)
        if ($appointment->status >= 3) {
            return back()->with('error', 'Không thể hủy ca khám đã diễn ra hoặc đã hoàn thành.');
        }

        $appointment->update([
            'status' => 6, // 6: Đã hủy
            'cancel_reason' => 'Tiếp tân hủy: ' . $request->cancel_reason,
        ]);

        return back()->with('success', 'Đã hủy lịch khám thành công.');
    }
}
