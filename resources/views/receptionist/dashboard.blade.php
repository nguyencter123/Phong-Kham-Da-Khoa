@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-info">
                <div class="card-header bg-info text-white">{{ __('Receptionist Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Xin chào Lễ tân, {{ Auth::user()->name }}!</h4>
                    <p>Chào mừng bạn đến với khu vực dành cho lễ tân.</p>
                    <hr>
                    <a href="{{ route('receptionist.appointments.index') }}" class="btn btn-primary">
                        <i class="fas fa-users-cog"></i> Quản lý Ca khám & Hàng đợi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
