@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">{{ __('Đặt lịch khám') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('patient.appointments.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="doctor_id" class="col-md-4 col-form-label text-md-end">{{ __('Chọn Bác sĩ/Chuyên khoa') }}</label>
                            <div class="col-md-6">
                                <select id="doctor_id" class="form-select" name="doctor_id">
                                    <option value="">-- Tôi không rõ - Để phòng khám sắp xếp --</option>
                                    @foreach($specialties as $specialty)
                                        <optgroup label="{{ $specialty->name }}">
                                            @foreach($specialty->doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                    {{ $doctor->title }} {{ $doctor->user->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date" class="col-md-4 col-form-label text-md-end">{{ __('Ngày khám') }}</label>
                            <div class="col-md-6">
                                <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->addWeek()->endOfWeek()->format('Y-m-d') }}" required>
                                @error('date')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="shift" class="col-md-4 col-form-label text-md-end">{{ __('Khung giờ') }}</label>
                            <div class="col-md-6">
                                <select id="shift" class="form-select @error('shift') is-invalid @enderror" name="shift" required disabled>
                                    <option value="">-- Vui lòng chọn ngày khám trước --</option>
                                </select>
                                @error('shift')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="reason" class="col-md-4 col-form-label text-md-end">{{ __('Triệu chứng sơ bộ / Lý do khám') }}</label>
                            <div class="col-md-6">
                                <textarea id="reason" class="form-control @error('reason') is-invalid @enderror" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                @error('reason')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Xác nhận đặt lịch') }}
                                </button>
                                <a href="{{ route('patient.appointments.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('date');
    const shiftSelect = document.getElementById('shift');

    function fetchShifts() {
        const date = dateInput.value;
        const doctorId = doctorSelect.value;

        if (!date) {
            shiftSelect.innerHTML = '<option value="">-- Vui lòng chọn ngày khám trước --</option>';
            shiftSelect.disabled = true;
            return;
        }

        shiftSelect.innerHTML = '<option value="">Đang tải khung giờ...</option>';
        shiftSelect.disabled = true;

        fetch(`{{ route('patient.appointments.shifts') }}?date=${date}&doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                shiftSelect.innerHTML = '<option value="">-- Chọn khung giờ --</option>';
                if (data.length === 0) {
                    shiftSelect.innerHTML = '<option value="">Không có lịch khám phù hợp vào ngày này</option>';
                    return;
                }
                
                data.forEach(shift => {
                    const label = shift === 'morning' ? 'Buổi Sáng' : 'Buổi Chiều';
                    const option = document.createElement('option');
                    option.value = shift;
                    option.textContent = label;
                    if ('{{ old("shift") }}' === shift) {
                        option.selected = true;
                    }
                    shiftSelect.appendChild(option);
                });
                shiftSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching shifts:', error);
                shiftSelect.innerHTML = '<option value="">Lỗi tải khung giờ</option>';
            });
    }

    doctorSelect.addEventListener('change', fetchShifts);
    dateInput.addEventListener('change', fetchShifts);

    if (dateInput.value) {
        fetchShifts();
    }
});
</script>
@endsection
