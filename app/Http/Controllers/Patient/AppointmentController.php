<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['doctor.user', 'doctor.specialty'])
            ->where('patient_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return view('patient.appointments.index', compact('appointments'));
    }

    public function history()
    {
        $appointments = Appointment::with([
            'doctor.user', 
            'medicalRecord.prescriptionDetails.medicine', 
            'invoice'
        ])
        ->where('patient_id', Auth::id())
        ->where('status', 5) // 5 = Hoàn thành
        ->orderBy('date', 'desc')
        ->get();

        return view('patient.appointments.history', compact('appointments'));
    }

    public function create()
    {
        $specialties = Specialty::with('doctors.user')->get();
        $doctors = Doctor::with(['user', 'specialty'])->get();

        return view('patient.appointments.create', compact('specialties', 'doctors'));
    }

    public function getAvailableShifts(Request $request)
    {
        $dateStr = $request->get('date');
        $doctorId = $request->get('doctor_id');

        if (!$dateStr) {
            return response()->json([]);
        }

        try {
            $date = Carbon::parse($dateStr);
            if ($date->isPast() && !$date->isToday()) {
                return response()->json([]);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        $dayOfWeek = $date->dayOfWeek;

        $shifts = ['morning', 'afternoon'];

        if ($doctorId) {
            // Hướng A: Check if doctor works on this day
            $workingShifts = Schedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->pluck('shift')
                ->toArray();
            
            return response()->json($workingShifts);
        }

        // Hướng B: Giả định phòng khám làm việc cả ngày (có thể tuỳ chỉnh theo logic clinic sau)
        return response()->json($shifts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'shift' => 'required|in:morning,afternoon',
            'reason' => 'required|string',
        ], [
            'reason.required' => 'Vui lòng cung cấp triệu chứng sơ bộ để chúng tôi sắp xếp bác sĩ phù hợp nhất.',
            'date.required' => 'Vui lòng chọn ngày khám.',
            'shift.required' => 'Vui lòng chọn ca khám.',
        ]);

        $patientId = Auth::id();
        $date = Carbon::parse($request->date);
        $doctorId = $request->doctor_id; // Có thể null

        // E5 - Xung đột lịch cá nhân: Bệnh nhân đã có lịch vào ngày này (Pending hoặc Approved)
        $hasConflict = Appointment::where('patient_id', $patientId)
            ->whereDate('date', $date->toDateString())
            ->whereIn('status', [0, 1])
            ->exists();

        if ($hasConflict) {
            return back()->with('error', 'Bạn đã có một lịch hẹn trong ngày này. Vui lòng kiểm tra Lịch sử khám.')->withInput();
        }

        if ($doctorId) {
            // E3 - Không có lịch làm việc
            $hasSchedule = Schedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('shift', $request->shift)
                ->exists();

            if (!$hasSchedule) {
                return back()->with('error', 'Bác sĩ không có lịch làm việc vào ngày này (hoặc ca này). Vui lòng chọn thời gian khác.')->withInput();
            }

            // E4 - Hết slot Online
            $schedule = Schedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('shift', $request->shift)
                ->first();

            $currentBookings = Appointment::where('doctor_id', $doctorId)
                ->whereDate('date', $date->toDateString())
                ->where('shift', $request->shift)
                ->where('status', '<', 6) // Trừ các lịch đã hủy
                ->count();

            if ($schedule && $currentBookings >= $schedule->max_patients_per_slot) {
                return back()->with('error', 'Khung giờ này đã hết chỗ đặt trước. Vui lòng chọn khung giờ khác.')->withInput();
            }
        } else {
            // Hướng B (Không chọn bác sĩ cụ thể) -> Có thể kiểm tra tổng capacity của toàn phòng khám nếu muốn
            // Ở đây đơn giản hoá là cho phép đặt, lễ tân sẽ phân công sau.
        }

        // Tạo cuộc hẹn mới
        Appointment::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId ?: null,
            'date' => $date->toDateString(),
            'shift' => $request->shift,
            'type' => 'online',
            'reason' => $request->reason,
            'status' => 0, // Chờ duyệt
        ]);

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Đặt lịch thành công! Yêu cầu của bạn đang được xử lý.');
    }
}
