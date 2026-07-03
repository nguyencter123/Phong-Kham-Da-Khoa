<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\Medicine;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\PrescriptionDetail;
use App\Models\Invoice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo Users cố định
        $admin = User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@clinic.com',
            'phone' => '0901000001',
            'citizen_id' => '001200000001',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $receptionist = User::create([
            'name' => 'Lễ tân Ngọc',
            'email' => 'receptionist@clinic.com',
            'phone' => '0901000002',
            'citizen_id' => '001200000002',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        $doctorUser1 = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'doctor@clinic.com',
            'phone' => '0901000003',
            'citizen_id' => '001200000003',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        $doctorUser2 = User::create([
            'name' => 'Trần Thị B',
            'email' => 'doctor2@clinic.com',
            'phone' => '0901000004',
            'citizen_id' => '001200000004',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        $doctorUser3 = User::create([
            'name' => 'Lê Đại C',
            'email' => 'doctor3@clinic.com',
            'phone' => '0901000005',
            'citizen_id' => '001200000005',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        $doctorUser4 = User::create([
            'name' => 'Phạm Văn D',
            'email' => 'doctor4@clinic.com',
            'phone' => '0901000007',
            'citizen_id' => '001200000007',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        $patientUser = User::create([
            'name' => 'Bệnh nhân Test',
            'email' => 'patient@clinic.com',
            'phone' => '0901000006',
            'citizen_id' => '001200000006',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        // 2. Tạo Bệnh nhân (Patient profile)
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'address' => '123 Đường Test, Quận 1, TP.HCM',
        ]);

        // 3. Tạo Chuyên khoa (Specialties)
        $specNoiKhoa = Specialty::create(['name' => 'Nội tổng quát', 'description' => 'Khám và điều trị các bệnh lý nội khoa cơ bản.']);
        $specNhi = Specialty::create(['name' => 'Nhi khoa', 'description' => 'Khám và điều trị các bệnh ở trẻ sơ sinh, trẻ em.']);
        $specTimMach = Specialty::create(['name' => 'Tim mạch', 'description' => 'Chuyên sâu về các bệnh lý tim mạch, huyết áp.']);
        $specRHM = Specialty::create(['name' => 'Răng Hàm Mặt', 'description' => 'Khám, nhổ răng, chỉnh nha và thẩm mỹ nụ cười.']);
        $specDaLieu = Specialty::create(['name' => 'Da liễu', 'description' => 'Điều trị mụn, dị ứng và các vấn đề về da.']);
        $specTaiMuiHong = Specialty::create(['name' => 'Tai Mũi Họng', 'description' => 'Khám và điều trị các bệnh lý về Tai Mũi Họng.']);
        $specMat = Specialty::create(['name' => 'Nhãn khoa', 'description' => 'Khám và điều trị các bệnh lý về Mắt.']);

        // 4. Tạo Bác sĩ (Doctors)
        $doctor1 = Doctor::create([
            'user_id' => $doctorUser1->id,
            'specialty_id' => $specNoiKhoa->id,
            'title' => 'Ths.BS',
            'bio' => 'Hơn 10 năm kinh nghiệm trong lĩnh vực Nội khoa.',
            'consultation_fee' => 150000,
        ]);

        $doctor2 = Doctor::create([
            'user_id' => $doctorUser2->id,
            'specialty_id' => $specNhi->id,
            'title' => 'BS.CKI',
            'bio' => 'Bác sĩ chuyên khoa I chuyên ngành Nhi khoa.',
            'consultation_fee' => 120000,
        ]);

        $doctor3 = Doctor::create([
            'user_id' => $doctorUser3->id,
            'specialty_id' => $specTimMach->id,
            'title' => 'PGS.TS',
            'bio' => 'Chuyên gia hàng đầu về tim mạch và phẫu thuật tim mạch.',
            'consultation_fee' => 300000,
        ]);

        $doctor4 = Doctor::create([
            'user_id' => $doctorUser4->id,
            'specialty_id' => $specRHM->id,
            'title' => 'BS.CKII',
            'bio' => 'Chuyên gia Răng Hàm Mặt, hơn 15 năm kinh nghiệm.',
            'consultation_fee' => 200000,
        ]);

        // 5. Tạo Lịch trực (Schedules)
        // Doctor 1 trực Thứ 2, Thứ 4, Thứ 6 (1, 3, 5 trong day_of_week)
        foreach ([1, 3, 5] as $day) {
            Schedule::create(['doctor_id' => $doctor1->id, 'day_of_week' => $day, 'shift' => 'morning', 'max_patients_per_slot' => 10]);
            Schedule::create(['doctor_id' => $doctor1->id, 'day_of_week' => $day, 'shift' => 'afternoon', 'max_patients_per_slot' => 10]);
        }
        
        // Doctor 2 trực Thứ 3, Thứ 5, Thứ 7 (2, 4, 6)
        foreach ([2, 4, 6] as $day) {
            Schedule::create(['doctor_id' => $doctor2->id, 'day_of_week' => $day, 'shift' => 'morning', 'max_patients_per_slot' => 10]);
            Schedule::create(['doctor_id' => $doctor2->id, 'day_of_week' => $day, 'shift' => 'afternoon', 'max_patients_per_slot' => 10]);
        }

        // Doctor 3 trực Chủ nhật và Thứ 2 (0, 1)
        foreach ([0, 1] as $day) {
            Schedule::create(['doctor_id' => $doctor3->id, 'day_of_week' => $day, 'shift' => 'morning', 'max_patients_per_slot' => 10]);
            Schedule::create(['doctor_id' => $doctor3->id, 'day_of_week' => $day, 'shift' => 'afternoon', 'max_patients_per_slot' => 10]);
        }

        // Doctor 4 trực các ngày làm việc (1, 2, 3, 4, 5)
        foreach ([1, 2, 3, 4, 5] as $day) {
            Schedule::create(['doctor_id' => $doctor4->id, 'day_of_week' => $day, 'shift' => 'morning', 'max_patients_per_slot' => 10]);
        }

        // 6. Tạo Danh mục Thuốc (Medicines)
        $medicines = [
            ['name' => 'Paracetamol 500mg', 'unit' => 'Viên', 'price' => 1500, 'stock' => 1000],
            ['name' => 'Amoxicillin 500mg', 'unit' => 'Viên', 'price' => 2000, 'stock' => 500],
            ['name' => 'Ibuprofen 400mg', 'unit' => 'Viên', 'price' => 2500, 'stock' => 800],
            ['name' => 'Vitamin C 1000mg', 'unit' => 'Viên', 'price' => 1000, 'stock' => 2000],
            ['name' => 'Oresol', 'unit' => 'Gói', 'price' => 5000, 'stock' => 300],
            ['name' => 'Loperamide 2mg', 'unit' => 'Viên', 'price' => 1200, 'stock' => 600],
            ['name' => 'Thuốc ho Bảo Thanh', 'unit' => 'Lọ', 'price' => 45000, 'stock' => 100],
            ['name' => 'Nước muối sinh lý 0.9%', 'unit' => 'Chai', 'price' => 5000, 'stock' => 500],
            ['name' => 'Alpha Choay', 'unit' => 'Viên', 'price' => 3000, 'stock' => 400],
            ['name' => 'Panadol Extra', 'unit' => 'Viên', 'price' => 2000, 'stock' => 1500],
            ['name' => 'Berberin', 'unit' => 'Lọ', 'price' => 25000, 'stock' => 200],
            ['name' => 'Ceftriaxone 1g', 'unit' => 'Lọ', 'price' => 35000, 'stock' => 150],
        ];

        $medModels = [];
        foreach ($medicines as $med) {
            $medModels[] = Medicine::create($med);
        }

        // 7. Tạo Ca khám hoàn thành (UC06) - Đã diễn ra 3 ngày trước
        $completedDate = Carbon::now()->subDays(3);
        $aptCompleted = Appointment::create([
            'patient_id' => $patientUser->id,
            'doctor_id' => $doctor1->id,
            'date' => $completedDate->toDateString(),
            'shift' => 'morning',
            'type' => 'online',
            'reason' => 'Đau đầu, chóng mặt kéo dài',
            'status' => 5, // Hoàn thành
            'created_at' => $completedDate->subDays(2),
            'updated_at' => $completedDate,
        ]);

        $medicalRecord = MedicalRecord::create([
            'appointment_id' => $aptCompleted->id,
            'symptoms' => 'Sốt nhẹ 38 độ, chóng mặt khi đứng lên, đau nửa đầu.',
            'diagnosis' => 'Thiếu máu lên não, cảm cúm virus.',
            'notes' => 'Uống nhiều nước, nghỉ ngơi hợp lý, kiêng gió lạnh.',
        ]);

        // Kê đơn thuốc cho ca khám này
        PrescriptionDetail::create([
            'medical_record_id' => $medicalRecord->id,
            'medicine_id' => $medModels[0]->id, // Paracetamol
            'quantity' => 10,
            'dosage' => 'Ngày 2 lần, mỗi lần 1 viên sau ăn.',
            'price_at_sale' => $medModels[0]->price,
        ]);
        PrescriptionDetail::create([
            'medical_record_id' => $medicalRecord->id,
            'medicine_id' => $medModels[3]->id, // Vitamin C
            'quantity' => 20,
            'dosage' => 'Ngày 1 viên sủi hòa tan vào buổi sáng.',
            'price_at_sale' => $medModels[3]->price,
        ]);
        PrescriptionDetail::create([
            'medical_record_id' => $medicalRecord->id,
            'medicine_id' => $medModels[8]->id, // Alpha Choay
            'quantity' => 15,
            'dosage' => 'Ngày 3 lần, mỗi lần 1 viên ngậm dưới lưỡi.',
            'price_at_sale' => $medModels[8]->price,
        ]);

        // Tạo hóa đơn
        $medicineFee = (10 * 1500) + (20 * 1000) + (15 * 3000); 
        Invoice::create([
            'appointment_id' => $aptCompleted->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'consultation_fee' => $doctor1->consultation_fee,
            'medicine_fee' => $medicineFee,
            'total_amount' => $doctor1->consultation_fee + $medicineFee,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'created_at' => $completedDate,
            'updated_at' => $completedDate,
        ]);

        // 8. Tạo Ca hẹn Pending (UC05) - Cho ngày mai
        $tomorrow = Carbon::now()->addDay();
        Appointment::create([
            'patient_id' => $patientUser->id,
            'doctor_id' => $doctor1->id,
            'date' => $tomorrow->toDateString(),
            'shift' => 'afternoon',
            'type' => 'online',
            'reason' => 'Tái khám theo lịch hẹn',
            'status' => 0, // Chờ duyệt
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}