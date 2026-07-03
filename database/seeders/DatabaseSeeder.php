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
        ]);
        User::create([
            'name' => 'Cao Phùng Phán',
            'email' => 'admin1@gmail.com',
            'phone' => '0353961884',
            'citizen_id' => '031002006967',
            'password' => Hash::make('123456'), 
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Tạo tài khoản Tiếp tân
        User::create([
            'name' => 'Tiếp Tân Số 1',
            'email' => 'receptionist@clinic.com',
            'phone' => '0901000002',
            'citizen_id' => '001200000002',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        $receptionist2 = User::create([
            'name' => 'Lễ tân Mai',
            'email' => 'receptionist2@clinic.com',
            'phone' => '0901000008',
            'citizen_id' => '001200000008',
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

        $extraDoctorUsers = [];
        $extraDoctors = [
            ['name' => 'Bac si Hoang Minh', 'email' => 'doctor5@clinic.com', 'phone' => '0901000010', 'citizen_id' => '001200000010'],
            ['name' => 'Bac si Do Thu Ha', 'email' => 'doctor6@clinic.com', 'phone' => '0901000011', 'citizen_id' => '001200000011'],
            ['name' => 'Bac si Vo Quoc Huy', 'email' => 'doctor7@clinic.com', 'phone' => '0901000012', 'citizen_id' => '001200000012'],
            ['name' => 'Bac si Nguyen Minh Anh', 'email' => 'doctor8@clinic.com', 'phone' => '0901000013', 'citizen_id' => '001200000013'],
            ['name' => 'Bac si Truong Thanh Son', 'email' => 'doctor9@clinic.com', 'phone' => '0901000014', 'citizen_id' => '001200000014'],
        ];

        foreach ($extraDoctors as $doctorData) {
            $extraDoctorUsers[] = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'phone' => $doctorData['phone'],
                'citizen_id' => $doctorData['citizen_id'],
                'password' => Hash::make('password'),
                'role' => 'doctor',
            ]);
        }

        $patientUser = User::create([
            'name' => 'Bệnh nhân Test',
            'email' => 'patient@clinic.com',
            'phone' => '0901000006',
            'citizen_id' => '001200000006',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        $patientUser2 = User::create([
            'name' => 'Bệnh nhân Lan',
            'email' => 'patient2@clinic.com',
            'phone' => '0901000009',
            'citizen_id' => '001200000009',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        // 2. Tạo Bệnh nhân (Patient profile)
        $extraPatientUsers = [];
        $extraPatients = [
            [
                'name' => 'Benh nhan Minh Khang',
                'email' => 'patient3@clinic.com',
                'phone' => '0901000015',
                'citizen_id' => '001200000015',
                'date_of_birth' => '1988-03-12',
                'gender' => 'male',
                'address' => '12 Le Loi, Quan 1, TP.HCM',
            ],
            [
                'name' => 'Benh nhan Thu Thao',
                'email' => 'patient4@clinic.com',
                'phone' => '0901000016',
                'citizen_id' => '001200000016',
                'date_of_birth' => '1994-09-08',
                'gender' => 'female',
                'address' => '88 Cach Mang Thang 8, Quan 3, TP.HCM',
            ],
            [
                'name' => 'Benh nhan Quoc Bao',
                'email' => 'patient5@clinic.com',
                'phone' => '0901000017',
                'citizen_id' => '001200000017',
                'date_of_birth' => '1979-11-25',
                'gender' => 'male',
                'address' => '31 Vo Van Tan, Quan 3, TP.HCM',
            ],
            [
                'name' => 'Benh nhan Ngoc Han',
                'email' => 'patient6@clinic.com',
                'phone' => '0901000018',
                'citizen_id' => '001200000018',
                'date_of_birth' => '2001-01-18',
                'gender' => 'female',
                'address' => '20 Phan Xich Long, Phu Nhuan, TP.HCM',
            ],
            [
                'name' => 'Benh nhan Gia Phuc',
                'email' => 'patient7@clinic.com',
                'phone' => '0901000019',
                'citizen_id' => '001200000019',
                'date_of_birth' => '2010-07-30',
                'gender' => 'male',
                'address' => '5 Nguyen Huu Canh, Binh Thanh, TP.HCM',
            ],
        ];

        foreach ($extraPatients as $patientData) {
            $extraPatientUsers[] = User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'phone' => $patientData['phone'],
                'citizen_id' => $patientData['citizen_id'],
                'password' => Hash::make('password'),
                'role' => 'patient',
            ]);
        }

        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'address' => '123 Đường Test, Quận 1, TP.HCM',
        ]);

        $patient2 = Patient::create([
            'user_id' => $patientUser2->id,
            'date_of_birth' => '1996-05-20',
            'gender' => 'female',
            'address' => '45 Nguyễn Trãi, Quận 5, TP.HCM',
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
        foreach ($extraPatients as $index => $patientData) {
            Patient::create([
                'user_id' => $extraPatientUsers[$index]->id,
                'date_of_birth' => $patientData['date_of_birth'],
                'gender' => $patientData['gender'],
                'address' => $patientData['address'],
            ]);
        }

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
        $extraDoctorProfiles = [
            [
                'specialty_id' => $specDaLieu->id,
                'title' => 'BS.CKI',
                'bio' => 'Bac si chuyen khoa Da lieu, dieu tri mun va di ung da.',
                'consultation_fee' => 180000,
            ],
            [
                'specialty_id' => $specTaiMuiHong->id,
                'title' => 'Ths.BS',
                'bio' => 'Bac si Tai Mui Hong, dieu tri viem xoang va viem hong.',
                'consultation_fee' => 160000,
            ],
            [
                'specialty_id' => $specMat->id,
                'title' => 'BS.CKII',
                'bio' => 'Bac si Nhan khoa, kham tat khuc xa va benh ly day mat.',
                'consultation_fee' => 220000,
            ],
            [
                'specialty_id' => $specNoiKhoa->id,
                'title' => 'TS.BS',
                'bio' => 'Bac si Noi tong quat, theo doi benh man tinh va tu van suc khoe.',
                'consultation_fee' => 250000,
            ],
            [
                'specialty_id' => $specTimMach->id,
                'title' => 'BS.CKI',
                'bio' => 'Bac si Tim mach, kham huyet ap, roi loan nhip va dau nguc.',
                'consultation_fee' => 240000,
            ],
        ];

        $extraDoctorModels = [];
        foreach ($extraDoctorProfiles as $index => $doctorProfile) {
            $extraDoctorModels[] = Doctor::create([
                'user_id' => $extraDoctorUsers[$index]->id,
                'specialty_id' => $doctorProfile['specialty_id'],
                'title' => $doctorProfile['title'],
                'bio' => $doctorProfile['bio'],
                'consultation_fee' => $doctorProfile['consultation_fee'],
            ]);
        }

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
        foreach ($extraDoctorModels as $index => $doctor) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                $shift = ($index + $day) % 2 === 0 ? 'morning' : 'afternoon';
                Schedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'shift' => $shift,
                    'max_patients_per_slot' => 8,
                ]);
            }
        }

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

        // 9. Tạo thêm dữ liệu mẫu để test hàng chờ bác sĩ và thanh toán
        $today = Carbon::today();

        Appointment::create([
            'patient_id' => $patientUser2->id,
            'doctor_id' => $doctor1->id,
            'date' => $today->toDateString(),
            'shift' => 'morning',
            'type' => 'offline',
            'reason' => 'Đau họng, sốt nhẹ',
            'status' => 2, // Đang chờ khám
            'is_priority' => true,
            'created_at' => Carbon::now()->subMinutes(40),
            'updated_at' => Carbon::now()->subMinutes(40),
        ]);

        $paymentAppointment = Appointment::create([
            'patient_id' => $patientUser2->id,
            'doctor_id' => $doctor2->id,
            'date' => $today->toDateString(),
            'shift' => 'afternoon',
            'type' => 'offline',
            'reason' => 'Ho kéo dài, nghẹt mũi',
            'status' => 4, // Chờ thanh toán
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now()->subHour(),
        ]);

        $paymentRecord = MedicalRecord::create([
            'appointment_id' => $paymentAppointment->id,
            'symptoms' => 'Ho khan, nghẹt mũi, mệt mỏi.',
            'diagnosis' => 'Viêm đường hô hấp trên.',
            'notes' => 'Uống nhiều nước, tái khám nếu sốt cao.',
        ]);

        PrescriptionDetail::create([
            'medical_record_id' => $paymentRecord->id,
            'medicine_id' => $medModels[0]->id,
            'quantity' => 8,
            'dosage' => 'Ngày 2 lần, mỗi lần 1 viên sau ăn.',
            'price_at_sale' => $medModels[0]->price,
        ]);

        PrescriptionDetail::create([
            'medical_record_id' => $paymentRecord->id,
            'medicine_id' => $medModels[6]->id,
            'quantity' => 1,
            'dosage' => 'Ngày 3 lần, mỗi lần 10ml.',
            'price_at_sale' => $medModels[6]->price,
        ]);

        $pendingMedicineFee = (8 * $medModels[0]->price) + $medModels[6]->price;

        Invoice::create([
            'appointment_id' => $paymentAppointment->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'consultation_fee' => $doctor2->consultation_fee,
            'medicine_fee' => $pendingMedicineFee,
            'total_amount' => $doctor2->consultation_fee + $pendingMedicineFee,
            'payment_method' => null,
            'payment_status' => 'unpaid',
        ]);
    }
}
