@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-danger text-white fw-bold text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> THANH TOÁN VIỆN PHÍ</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Mã Hóa Đơn:</h6>
                            <h5 class="fw-bold">{{ $invoice->invoice_number }}</h5>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted">Ngày lập:</h6>
                            <h5 class="fw-bold">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y H:i') }}</h5>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted border-bottom pb-2 mb-3">Thông tin Bệnh nhân</h6>
                            <p class="mb-1"><strong>Họ tên:</strong> <span class="text-primary fw-bold">{{ $appointment->patient->name ?? 'N/A' }}</span></p>
                            <p class="mb-1"><strong>SĐT:</strong> {{ $appointment->patient->phone ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Bác sĩ khám:</strong> {{ $appointment->doctor->title ?? '' }} {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Chẩn đoán:</strong> <span class="text-danger">{{ $appointment->medicalRecord->diagnosis ?? 'Không có thông tin' }}</span></p>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <h6 class="text-muted border-bottom pb-2 mb-3">Chi tiết Chi phí</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Nội dung</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-user-md text-primary"></i> Phí khám bệnh (Khám cố định)</td>
                                    <td class="text-end">{{ number_format($invoice->consultation_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @if($appointment->medicalRecord && $appointment->medicalRecord->prescriptionDetails->count() > 0)
                                    <tr>
                                        <td colspan="2" class="fw-bold"><i class="fas fa-pills text-success"></i> Tiền Thuốc:</td>
                                    </tr>
                                    @foreach($appointment->medicalRecord->prescriptionDetails as $pd)
                                        <tr>
                                            <td class="ps-4">
                                                - {{ $pd->medicine->name }} <br>
                                                <small class="text-muted">Số lượng: {{ $pd->quantity }} {{ $pd->medicine->unit }} x {{ number_format($pd->price_at_sale, 0, ',', '.') }}đ</small>
                                            </td>
                                            <td class="text-end align-middle">{{ number_format($pd->quantity * $pd->price_at_sale, 0, ',', '.') }} đ</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-muted fst-italic">Bệnh nhân không có đơn thuốc.</td>
                                    </tr>
                                @endif
                                
                                <tr class="border-top border-2 border-dark">
                                    <td class="fw-bold fs-5 text-end pt-3">TỔNG CỘNG:</td>
                                    <td class="fw-bold fs-4 text-danger text-end pt-3">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="row gx-3">
                        <div class="col-md-6 mb-2">
                            <form action="{{ route('receptionist.payment.cash', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5" onclick="return confirm('Xác nhận đã nhận đủ {{ number_format($invoice->total_amount, 0, ',', '.') }}đ tiền mặt từ khách hàng?')">
                                    <i class="fas fa-money-bill-wave fa-lg"></i> TIỀN MẶT
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 mb-2">
                            <form action="{{ route('receptionist.payment.vnpay', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 py-3 fw-bold fs-5">
                                    <i class="fas fa-credit-card fa-lg"></i> QUẸT THẺ / VNPAY
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('receptionist.appointments.index') }}" class="text-muted text-decoration-none"><i class="fas fa-arrow-left"></i> Quay lại Danh sách</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
