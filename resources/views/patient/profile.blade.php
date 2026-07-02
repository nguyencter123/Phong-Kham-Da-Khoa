@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">{{ __('Cập nhật thông tin cá nhân') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('patient.profile.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Họ và tên') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Số điện thoại') }}</label>
                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="citizen_id" class="col-md-4 col-form-label text-md-end">{{ __('Số căn cước (CCCD)') }}</label>
                            <div class="col-md-6">
                                <input id="citizen_id" type="text" class="form-control @error('citizen_id') is-invalid @enderror" name="citizen_id" value="{{ old('citizen_id', $user->citizen_id) }}">
                                @error('citizen_id')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date_of_birth" class="col-md-4 col-form-label text-md-end">{{ __('Ngày sinh') }}</label>
                            <div class="col-md-6">
                                <input id="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth) }}">
                                @error('date_of_birth')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="gender" class="col-md-4 col-form-label text-md-end">{{ __('Giới tính') }}</label>
                            <div class="col-md-6">
                                <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                                    <option value="">Chọn giới tính...</option>
                                    <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('gender')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <hr>
                        <h5 class="text-center">Đổi mật khẩu (Bỏ trống nếu không đổi)</h5>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mật khẩu mới') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Xác nhận mật khẩu mới') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Lưu thay đổi') }}
                                </button>
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
