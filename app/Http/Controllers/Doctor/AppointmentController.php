<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\PrescriptionDetail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function examine($id)
    {
        $doctor = Auth::user()->doctor;

        $appointment = Appointment::with(['patient.patient'])
            ->where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 3) // Examining
            ->firstOrFail();

        // Lấy lịch sử khám bệnh của bệnh nhân này
        $history = MedicalRecord::with(['appointment.doctor.user', 'prescriptionDetails.medicine'])
            ->whereHas('appointment', function($q) use ($appointment) {
                $q->where('patient_id', $appointment->patient_id)
                  ->where('status', '>=', 4); // Khám xong hoặc đã hoàn thành
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy danh sách thuốc để hiển thị trong dropdown
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name')->get();

        return view('doctor.appointments.examine', compact('appointment', 'history', 'medicines'));
    }

    public function storeExamine(Request $request, $id)
    {
        $doctor = Auth::user()->doctor;

        $appointment = Appointment::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 3)
            ->firstOrFail();

        // E1: Bỏ trống Chẩn đoán
        $request->validate([
            'diagnosis' => 'required|string',
            'symptoms' => 'required|string',
            'notes' => 'nullable|string',
            // Thuốc là mảng (nếu có)
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.dosage' => 'required|string',
        ], [
            'diagnosis.required' => 'Vui lòng nhập chẩn đoán bệnh trước khi hoàn tất.',
            'symptoms.required' => 'Vui lòng nhập triệu chứng lâm sàng.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Tạo Medical Record
            $medicalRecord = MedicalRecord::create([
                'appointment_id' => $appointment->id,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'notes' => $request->notes,
            ]);

            $totalMedicineFee = 0;

            // 2. Kê đơn & Trừ tồn kho
            if ($request->has('medicines') && count($request->medicines) > 0) {
                foreach ($request->medicines as $medData) {
                    $medicine = Medicine::lockForUpdate()->find($medData['id']);
                    
                    // E2: Số lượng vượt tồn kho
                    if ($medData['quantity'] > $medicine->stock) {
                        DB::rollBack();
                        return back()->withInput()->with('error', "Thuốc [{$medicine->name}] chỉ còn lại {$medicine->stock} đơn vị trong kho. Vui lòng điều chỉnh lại số lượng.");
                    }

                    // Trừ kho
                    $medicine->stock -= $medData['quantity'];
                    $medicine->save();

                    // Lưu chi tiết đơn thuốc
                    PrescriptionDetail::create([
                        'medical_record_id' => $medicalRecord->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $medData['quantity'],
                        'dosage' => $medData['dosage'],
                        'price_at_sale' => $medicine->price,
                    ]);

                    $totalMedicineFee += ($medicine->price * $medData['quantity']);
                }
            }

            // 3. Tạo Hóa đơn (Invoice)
            $consultationFee = $doctor->consultation_fee ?? 200000;
            Invoice::create([
                'appointment_id' => $appointment->id,
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'consultation_fee' => $consultationFee,
                'medicine_fee' => $totalMedicineFee,
                'total_amount' => $consultationFee + $totalMedicineFee,
                'payment_status' => 'unpaid',
            ]);

            // 4. Cập nhật trạng thái Appointment -> 4
            $appointment->status = 4;
            $appointment->save();

            DB::commit();

            return redirect()->route('doctor.dashboard')->with('success', 'Đã lưu hồ sơ bệnh án thành công. Bệnh nhân đã được chuyển ra quầy chờ thanh toán.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
