<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return abort(403, 'Tài khoản chưa được thiết lập hồ sơ bác sĩ.');
        }

        $appointments = Appointment::with(['patient.patient'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', Carbon::today())
            ->whereIn('status', [2, 3]) // 2 = waiting, 3 = examining
            ->orderBy('status', 'desc') // Ưu tiên ca đang khám (3) lên đầu
            ->orderBy('is_priority', 'desc')
            ->orderBy('updated_at', 'asc')
            ->get();

        $schedules = \App\Models\Schedule::where('doctor_id', $doctor->id)
            ->orderByRaw('day_of_week = 0, day_of_week') // Sắp T2 -> CN
            ->orderBy('shift', 'asc') // Sáng -> Chiều
            ->get();

        return view('doctor.dashboard', compact('appointments', 'schedules'));
    }

    public function startExamine($id)
    {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 2)
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Ca khám này không tồn tại hoặc đã được chuyển trạng thái.');
        }

        $appointment->status = 3; // Examining
        $appointment->save();

        return redirect()->route('doctor.appointments.examine', $id);
    }

    public function pushBack($id)
    {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 2)
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Ca khám này không còn nằm trong hàng chờ.');
        }

        // Cập nhật updated_at thành thời gian hiện tại để lùi xuống cuối danh sách
        $appointment->touch();

        return back()->with('success', 'Đã lùi bệnh nhân xuống cuối hàng chờ.');
    }
}
