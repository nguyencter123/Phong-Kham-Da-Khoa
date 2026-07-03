@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>{{ __('Lịch sử khám bệnh') }}</span>
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-sm btn-light text-primary fw-bold">Đặt lịch khám mới</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($appointments->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Ngày khám</th>
                                    <th>Ca khám</th>
                                    <th>Bác sĩ</th>
                                    <th>Lý do khám</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $apt)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($apt->date)->format('d/m/Y') }}</td>
                                    <td>{{ $apt->shift == 'morning' ? 'Sáng' : 'Chiều' }}</td>
                                    <td>
                                        @if($apt->doctor)
                                            {{ $apt->doctor->title }} {{ $apt->doctor->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $apt->doctor->specialty->name }}</small>
                                        @else
                                            <span class="text-muted">Phòng khám sắp xếp</span>
                                        @endif
                                    </td>
                                    <td>{{ $apt->reason }}</td>
                                    <td>
                                        @if($apt->status == 0)
                                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                        @elseif($apt->status == 1)
                                            <span class="badge bg-success">Đã duyệt</span>
                                        @elseif($apt->status == 6)
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @else
                                            <span class="badge bg-info">Đã xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted my-4">Bạn chưa có lịch hẹn nào. Hãy đặt lịch khám ngay!</p>
                        <div class="text-center">
                            <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">Đặt lịch khám</a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
