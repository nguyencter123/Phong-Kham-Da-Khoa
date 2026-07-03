<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::with('user', 'specialty')->get();
        $query = Schedule::with('doctor.user');

        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $query->where('doctor_id', $request->doctor_id);
        }

        $schedules = $query->join('doctors', 'schedules.doctor_id', '=', 'doctors.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->select('schedules.*')
            ->orderByRaw("SUBSTRING_INDEX(users.name, ' ', -1) ASC") // Sắp xếp theo Tên (từ cuối cùng)
            ->orderBy('users.name') // Nếu trùng Tên thì xếp theo Họ và chữ lót
            ->orderByRaw('day_of_week = 0, day_of_week') // Đẩy Chủ nhật (0) xuống cuối tuần, sắp từ T2->CN
            ->orderBy('shift', 'asc') // Sắp 'morning' trước 'afternoon' dựa theo thứ tự ENUM trong DB
            ->get();

        return view('admin.schedules.index', compact('schedules', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'shift' => 'required|in:morning,afternoon',
            'max_patients_per_slot' => 'required|integer|min:1',
        ]);

        // E1: Xung đột ca làm việc
        $conflict = Schedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('shift', $request->shift)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['conflict' => 'Ca làm việc này đã được thiết lập cho bác sĩ. Vui lòng kiểm tra lại.'])->withInput();
        }

        Schedule::create($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Thêm ca làm việc thành công');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'shift' => 'required|in:morning,afternoon',
            'max_patients_per_slot' => 'required|integer|min:1',
        ]);

        // Rào chắn 1: Khóa đổi thuộc tính cốt lõi (Bác sĩ, Ngày, Ca) nếu đã có lịch hẹn tương lai theo lịch cũ
        if ($schedule->doctor_id != $request->doctor_id || $schedule->day_of_week != $request->day_of_week || $schedule->shift != $request->shift) {
            $hasFutureAppointments = Appointment::where('doctor_id', $schedule->doctor_id)
                ->where('date', '>=', Carbon::today()->toDateString())
                ->whereRaw('DAYOFWEEK(date) - 1 = ?', [$schedule->day_of_week])
                ->where('shift', $schedule->shift)
                ->exists();

            if ($hasFutureAppointments) {
                return back()->withErrors(['conflict' => 'Không thể đổi Bác sĩ, Ngày hoặc Ca vì lịch cũ đã có bệnh nhân đặt trong tương lai. Vui lòng xóa lịch cũ (nếu được) và tạo lịch mới.'])->withInput();
            }
        }

        // Rào chắn 2: Khóa giảm số lượng bệnh nhân (Capacity Reduction)
        // Tìm ngày có số lượng đặt đông nhất trong tương lai cho lịch này
        $maxBooked = Appointment::selectRaw('date, count(*) as total')
            ->where('doctor_id', $request->doctor_id)
            ->where('date', '>=', Carbon::today()->toDateString())
            ->whereRaw('DAYOFWEEK(date) - 1 = ?', [$request->day_of_week])
            ->where('shift', $request->shift)
            ->groupBy('date')
            ->orderByDesc('total')
            ->first();

        if ($maxBooked && $request->max_patients_per_slot < $maxBooked->total) {
            return back()->withErrors(['max_patients' => 'Không thể giảm số lượng tối đa xuống ' . $request->max_patients_per_slot . ' vì ngày ' . Carbon::parse($maxBooked->date)->format('d/m/Y') . ' đã có ' . $maxBooked->total . ' bệnh nhân đặt lịch.'])->withInput();
        }

        // E1: Xung đột ca làm việc
        $conflict = Schedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('shift', $request->shift)
            ->where('id', '!=', $id)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['conflict' => 'Ca làm việc này đã được thiết lập cho bác sĩ. Vui lòng kiểm tra lại.'])->withInput();
        }

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Cập nhật ca làm việc thành công');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        // E3: Ràng buộc khi xóa lịch (Kiểm tra lịch hẹn tương lai)
        $hasFutureAppointments = Appointment::where('doctor_id', $schedule->doctor_id)
            ->where('date', '>=', Carbon::today()->toDateString())
            ->whereRaw('DAYOFWEEK(date) - 1 = ?', [$schedule->day_of_week])
            ->where('shift', $schedule->shift)
            ->exists();

        if ($hasFutureAppointments) {
            return back()->with('error', 'Không thể xóa ca làm việc này vì đã có bệnh nhân đặt lịch trong các tuần tới. Vui lòng xử lý các lịch hẹn tương lai trước.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Xóa ca làm việc thành công');
    }
    public function toggleActive($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->is_active = !$schedule->is_active;
        $schedule->save();

        $status = $schedule->is_active ? 'mở khóa' : 'khóa';
        return back()->with('success', "Đã $status ca làm việc thành công.");
    }
}
