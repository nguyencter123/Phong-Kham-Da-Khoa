@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="mb-4 text-primary"><i class="fas fa-notes-medical"></i> Lịch sử khám bệnh</h3>

            @if($appointments->count() == 0)
                <div class="alert alert-info text-center py-5">
                    <h5 class="mb-3">Bạn chưa có bản ghi lịch sử khám bệnh nào tại phòng khám.</h5>
                    <p class="text-muted mb-4">Các ca khám đã hoàn thành sẽ được lưu trữ và hiển thị tại đây.</p>
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary btn-lg">Đặt lịch khám ngay</a>
                </div>
            @else
                @foreach($appointments as $index => $apt)
                    <div class="card mb-4 shadow-sm border-start border-primary border-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <strong>Ngày khám: {{ \Carbon\Carbon::parse($apt->date)->format('d/m/Y') }} - {{ $apt->shift == 'morning' ? 'Buổi Sáng' : 'Buổi Chiều' }}</strong>
                            <span class="badge bg-success">Đã hoàn thành</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Bác sĩ điều trị</h6>
                                    <p class="mb-0 fw-bold">{{ $apt->doctor ? $apt->doctor->title . ' ' . $apt->doctor->user->name : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Tổng chi phí</h6>
                                    <p class="mb-0 fw-bold text-danger">{{ $apt->invoice ? number_format($apt->invoice->total_amount, 0, ',', '.') . ' VNĐ' : 'Chưa có hóa đơn' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Triệu chứng lâm sàng</h6>
                                    <p class="mb-0">{{ $apt->medicalRecord ? $apt->medicalRecord->symptoms : 'Không có' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Chẩn đoán của bác sĩ</h6>
                                    <p class="mb-0 text-primary fw-bold">{{ $apt->medicalRecord ? $apt->medicalRecord->diagnosis : 'Không có' }}</p>
                                </div>
                            </div>

                            @if($apt->medicalRecord && $apt->medicalRecord->prescriptionDetails->count() > 0)
                                <hr>
                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#prescription-{{ $apt->id }}" aria-expanded="false">
                                    Xem chi tiết đơn thuốc
                                </button>
                                
                                <div class="collapse mt-3" id="prescription-{{ $apt->id }}">
                                    <div class="card card-body bg-light">
                                        <h6 class="mb-3 text-secondary">Đơn thuốc chỉ định:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered bg-white mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Tên thuốc</th>
                                                        <th>Số lượng</th>
                                                        <th>Đơn vị</th>
                                                        <th>Liều dùng</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($apt->medicalRecord->prescriptionDetails as $detail)
                                                    <tr>
                                                        <td>{{ $detail->medicine->name }}</td>
                                                        <td>{{ $detail->quantity }}</td>
                                                        <td>{{ $detail->medicine->unit }}</td>
                                                        <td>{{ $detail->dosage }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="mt-4">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
