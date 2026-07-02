@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">{{ __('Patient Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Xin chào Bệnh nhân, {{ Auth::user()->name }}!</h4>
                    <p>Chào mừng bạn đến với khu vực dành cho bệnh nhân.</p>
                    <hr>
                    <a href="{{ route('patient.profile') }}" class="btn btn-primary">Cập nhật thông tin cá nhân</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
