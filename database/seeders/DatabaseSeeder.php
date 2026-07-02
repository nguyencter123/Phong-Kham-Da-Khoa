<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Medicine;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin quản trị cao nhất
        User::create([
            'name' => 'Phạm Lương Nguyên',
            'email' => 'admin@clinic.com',
            'phone' => '0988776967',
            'citizen_id' => '001002006967',
            'password' => Hash::make('1'), 
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Tạo tài khoản Tiếp tân
        User::create([
            'name' => 'Tiếp Tân Số 1',
            'email' => 'receptionist@clinic.com',
            'phone' => '0911223344',
            'citizen_id' => '001002001111',
            'password' => Hash::make('1'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // 3. Tạo 2 Chuyên khoa cơ bản
        $khoaNoi = Specialty::create([
            'name' => 'Khoa Nội',
            'description' => 'Chuyên khám các bệnh lý nội khoa chung.',
        ]);

        $khoaNhi = Specialty::create([
            'name' => 'Khoa Nhi',
            'description' => 'Chuyên khám và điều trị bệnh cho trẻ em.',
        ]);

        // 4. Tạo 1 tài khoản Bác sĩ và Hồ sơ bác sĩ
        $userDoctor = User::create([
            'name' => 'BS. Trần Văn A',
            'email' => 'doctor@clinic.com',
            'phone' => '0922334455',
            'citizen_id' => '001002002222',
            'password' => Hash::make('1'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        Doctor::create([
            'user_id' => $userDoctor->id,
            'specialty_id' => $khoaNoi->id,
            'title' => 'ThS. BS',
            'bio' => 'Bác sĩ có 10 năm kinh nghiệm trong lĩnh vực Nội tổng hợp.',
            'consultation_fee' => 150000,
        ]);

        // 5. Tạo một số loại thuốc vào kho
        Medicine::create(['name' => 'Paracetamol 500mg', 'unit' => 'Viên', 'price' => 2000, 'stock' => 1000]);
        Medicine::create(['name' => 'Amoxicillin 250mg', 'unit' => 'Viên', 'price' => 5000, 'stock' => 500]);
        Medicine::create(['name' => 'Siro Ho Bảo Thanh', 'unit' => 'Chai', 'price' => 45000, 'stock' => 50]);
    }
}