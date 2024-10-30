@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Approve Bookings</h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Room Name</th>
                    <th scope="col">Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->room->name }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>
                            @if ($booking->approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if (!$booking->approved)
                                <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>Already Approved</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
