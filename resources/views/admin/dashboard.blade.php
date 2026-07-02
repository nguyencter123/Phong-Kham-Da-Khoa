@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Xin chào Admin, {{ Auth::user()->name }}!</h4>
                    <p>Chào mừng bạn đến với khu vực quản trị viên.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
