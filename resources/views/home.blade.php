@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">{{ __('Sidebar') }}</div>
                <div class="list-group list-group-flush">
                    <!-- Sidebar untuk semua pengguna -->
                    <a href="{{ route('bookings.create') }}" class="list-group-item list-group-item-action">Form Booking Meeting Room</a>

                    <!-- Sidebar hanya untuk admin -->
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ route('rooms.index') }}" class="list-group-item list-group-item-action">Rooms</a>
                        <a href="{{ route('admin.bookings.index') }}" class="list-group-item list-group-item-action">Approve Booking</a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
