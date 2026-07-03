@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Phần 1: Thông tin bệnh nhân & Lịch sử -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-user-injured"></i> Thông tin Bệnh nhân
                </div>
                <div class="card-body">
                    <h5 class="text-primary fw-bold">{{ $appointment->patient->name ?? 'N/A' }}</h5>
                    @php
                        $patientProfile = $appointment->patient->patient ?? null;
                        $age = $patientProfile && $patientProfile->date_of_birth ? \Carbon\Carbon::parse($patientProfile->date_of_birth)->age : 'N/A';
                        $gender = $patientProfile && $patientProfile->gender == 'male' ? 'Nam' : ($patientProfile && $patientProfile->gender == 'female' ? 'Nữ' : 'Khác');
                    @endphp
                    <p class="mb-1"><strong>Tuổi:</strong> {{ $age }} | <strong>Giới tính:</strong> {{ $gender }}</p>
                    <p class="mb-1"><strong>SĐT:</strong> {{ $appointment->patient->phone ?? 'N/A' }}</p>
                    <hr>
                    <h6 class="fw-bold text-secondary">Lịch sử khám bệnh</h6>
                    @if($history->count() > 0)
                        <div class="accordion" id="historyAccordion">
                            @foreach($history as $idx => $record)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $idx }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}" aria-expanded="false" aria-controls="collapse{{ $idx }}">
                                        Ngày: {{ \Carbon\Carbon::parse($record->created_at)->format('d/m/Y') }} 
                                        <span class="badge bg-secondary ms-2">{{ $record->appointment->doctor->user->name ?? 'N/A' }}</span>
                                    </button>
                                </h2>
                                <div id="collapse{{ $idx }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $idx }}" data-bs-parent="#historyAccordion">
                                    <div class="accordion-body small">
                                        <p class="mb-1"><strong>CĐ:</strong> <span class="text-danger">{{ $record->diagnosis }}</span></p>
                                        <p class="mb-1"><strong>Đơn thuốc:</strong></p>
                                        <ul class="mb-0 ps-3">
                                            @foreach($record->prescriptionDetails as $pd)
                                            <li>{{ $pd->medicine->name }} ({{ $pd->quantity }} {{ $pd->medicine->unit }}) - {{ $pd->dosage }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small">Không có lịch sử khám.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Phần 2 & 3: Bệnh án & Kê đơn -->
        <div class="col-md-8">
            <form action="{{ route('doctor.appointments.storeExamine', $appointment->id) }}" method="POST" id="examineForm">
                @csrf
                <!-- Phần 2: Bệnh án -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fas fa-file-medical"></i> Hồ sơ Bệnh án
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Triệu chứng lâm sàng <span class="text-danger">*</span></label>
                            <textarea name="symptoms" class="form-control" rows="3" required>{{ old('symptoms', $appointment->reason) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chẩn đoán bệnh <span class="text-danger">*</span></label>
                            <textarea name="diagnosis" class="form-control" rows="2" required placeholder="Nhập chẩn đoán chính xác...">{{ old('diagnosis') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lời dặn / Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Ví dụ: Kiêng đồ cay nóng...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Phần 3: Kê đơn thuốc -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-pills"></i> Kê đơn thuốc (Không bắt buộc)</span>
                        <button type="button" class="btn btn-sm btn-light text-success fw-bold" id="btnAddMedicine">
                            <i class="fas fa-plus"></i> Thêm thuốc
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="medicinesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Tên Thuốc (Kho)</th>
                                        <th width="15%">SL</th>
                                        <th width="35%">Liều dùng / Cách dùng</th>
                                        <th width="10%" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dòng thuốc sẽ được thêm vào đây bằng JS -->
                                </tbody>
                            </table>
                            <div id="emptyMedicineRow" class="text-center py-4 text-muted">
                                Bệnh nhân không cần dùng thuốc.
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end py-3">
                        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary px-4 me-2">Quay lại</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fas fa-save"></i> Hoàn tất khám & Lưu bệnh án
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Dòng thuốc ẩn cho JS -->
<template id="medicineRowTemplate">
    <tr>
        <td>
            <select name="medicines[__INDEX__][id]" class="form-select medicine-select" required>
                <option value="">-- Chọn thuốc --</option>
                @foreach($medicines as $med)
                    <option value="{{ $med->id }}" data-stock="{{ $med->stock }}" data-unit="{{ $med->unit }}">
                        {{ $med->name }} (Còn: {{ $med->stock }} {{ $med->unit }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <div class="input-group">
                <input type="number" name="medicines[__INDEX__][quantity]" class="form-control quantity-input" min="1" required placeholder="SL">
                <span class="input-group-text unit-label">--</span>
            </div>
        </td>
        <td>
            <input type="text" name="medicines[__INDEX__][dosage]" class="form-control" required placeholder="VD: Sáng 1, tối 1 sau ăn">
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-danger btn-remove-med"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let medIndex = 0;
    const btnAddMedicine = document.getElementById('btnAddMedicine');
    const tableBody = document.querySelector('#medicinesTable tbody');
    const emptyMsg = document.getElementById('emptyMedicineRow');
    const template = document.getElementById('medicineRowTemplate').innerHTML;

    btnAddMedicine.addEventListener('click', function() {
        emptyMsg.style.display = 'none';
        
        let newRowHtml = template.replace(/__INDEX__/g, medIndex);
        tableBody.insertAdjacentHTML('beforeend', newRowHtml);
        
        let newRow = tableBody.lastElementChild;
        
        // Cập nhật đơn vị thuốc khi chọn
        let select = newRow.querySelector('.medicine-select');
        let unitLabel = newRow.querySelector('.unit-label');
        let qtyInput = newRow.querySelector('.quantity-input');
        
        select.addEventListener('change', function() {
            let option = this.options[this.selectedIndex];
            if(option.value) {
                unitLabel.textContent = option.getAttribute('data-unit');
                qtyInput.max = option.getAttribute('data-stock'); 
            } else {
                unitLabel.textContent = '--';
                qtyInput.max = '';
            }
        });

        // Xóa dòng
        let btnRemove = newRow.querySelector('.btn-remove-med');
        btnRemove.addEventListener('click', function() {
            newRow.remove();
            if(tableBody.children.length === 0) {
                emptyMsg.style.display = 'block';
            }
        });

        medIndex++;
    });
});
</script>
@endsection
