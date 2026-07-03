<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Trả về giao diện báo cáo
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Lấy dữ liệu báo cáo qua AJAX
     */
    public function getData(Request $request)
    {
        $dateRange = $request->input('date_range', 'this_month');
        $start = null;
        $end = null;

        $now = Carbon::now();

        switch ($dateRange) {
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'custom':
                if (!$request->start_date || !$request->end_date) {
                    return response()->json(['error' => 'Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.']);
                }
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // E2 - Lỗi truy xuất dữ liệu lớn
        if ($start->diffInDays($end) > 365) {
            return response()->json([
                'error' => 'Dữ liệu quá lớn để hiển thị trực tiếp. Vui lòng thu hẹp khoảng thời gian (tối đa 1 năm) hoặc sử dụng chức năng Xuất báo cáo Excel.'
            ]);
        }

        // 1. KPIs
        $totalRevenue = Invoice::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // Status 5: Hoàn thành, >= 6: Hủy (ví dụ 6: bệnh nhân hủy, 7: bác sĩ hủy)
        $completedAppointments = Appointment::where('status', 5)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $cancelledAppointments = Appointment::where('status', '>=', 6)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->count();

        // 2. Dữ liệu Biểu đồ Doanh thu (Line Chart) theo ngày
        // Gom nhóm theo ngày
        $revenueByDay = Invoice::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $lineChartLabels = [];
        $lineChartData = [];
        
        // Điền đầy đủ các ngày trong khoảng thời gian để biểu đồ không bị ngắt quãng
        $currentDate = $start->copy();
        $mappedRevenue = $revenueByDay->pluck('total', 'date')->toArray();

        while ($currentDate->lte($end)) {
            $dateStr = $currentDate->format('Y-m-d');
            $lineChartLabels[] = $currentDate->format('d/m');
            $lineChartData[] = $mappedRevenue[$dateStr] ?? 0;
            $currentDate->addDay();
        }

        // 3. Dữ liệu Biểu đồ Tròn (Pie Chart) theo Chuyên khoa
        $appointmentsBySpecialty = Appointment::where('appointments.status', 5)
            ->whereBetween('appointments.date', [$start->toDateString(), $end->toDateString()])
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('specialties.name', DB::raw('COUNT(appointments.id) as total'))
            ->groupBy('specialties.id', 'specialties.name')
            ->get();

        $pieChartLabels = $appointmentsBySpecialty->pluck('name')->toArray();
        $pieChartData = $appointmentsBySpecialty->pluck('total')->toArray();

        // 4. Danh sách Bác sĩ nổi bật (Top 5)
        $topDoctors = Appointment::where('appointments.status', 5)
            ->whereBetween('appointments.date', [$start->toDateString(), $end->toDateString()])
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->join('specialties', 'doctors.specialty_id', '=', 'specialties.id')
            ->select('users.name as doctor_name', 'specialties.name as specialty_name', DB::raw('COUNT(appointments.id) as total'))
            ->groupBy('doctors.id', 'users.name', 'specialties.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'kpis' => [
                'total_revenue' => $totalRevenue,
                'completed' => $completedAppointments,
                'cancelled' => $cancelledAppointments,
            ],
            'charts' => [
                'line' => [
                    'labels' => $lineChartLabels,
                    'data' => $lineChartData,
                ],
                'pie' => [
                    'labels' => $pieChartLabels,
                    'data' => $pieChartData,
                ]
            ],
            'top_doctors' => $topDoctors
        ]);
    }

    /**
     * Xuất báo cáo ra CSV
     */
    public function export(Request $request)
    {
        // Tương tự xử lý thời gian như getData
        $dateRange = $request->input('date_range', 'this_month');
        $start = null;
        $end = null;
        $now = Carbon::now();

        switch ($dateRange) {
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'custom':
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        $invoices = Invoice::with(['appointment.doctor.user', 'appointment.patient.patient'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $fileName = 'Bao_Cao_Doanh_Thu_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Mã HĐ', 'Ngày thanh toán', 'Bệnh nhân', 'Bác sĩ', 'Phương thức', 'Tổng tiền (VND)');

        $callback = function() use($invoices, $columns) {
            $file = fopen('php://output', 'w');
            // Thêm BOM để Excel đọc đúng tiếng Việt
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($invoices as $invoice) {
                $row['Mã HĐ']  = $invoice->invoice_number;
                $row['Ngày thanh toán']    = $invoice->created_at->format('d/m/Y H:i');
                $row['Bệnh nhân']  = $invoice->appointment->patient->patient->name ?? 'N/A';
                $row['Bác sĩ']  = $invoice->appointment->doctor->user->name ?? 'N/A';
                $row['Phương thức']  = strtoupper($invoice->payment_method);
                $row['Tổng tiền (VND)']  = $invoice->total_amount;

                fputcsv($file, array($row['Mã HĐ'], $row['Ngày thanh toán'], $row['Bệnh nhân'], $row['Bác sĩ'], $row['Phương thức'], $row['Tổng tiền (VND)']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
