@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="my-4">Rooms</h1>

            <!-- Tombol Tambah Room -->
            <a href="{{ route('rooms.create') }}" class="btn btn-success mb-3">
                <i class="fas fa-plus-circle"></i> Add Room
            </a>

            <!-- Tabel Daftar Ruangan -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Room List</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Room Name</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rooms as $room)
                                <tr>
                                    <td>{{ $room->name }}</td>
                                    <td class="text-end">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <!-- Form Hapus dengan konfirmasi -->
                                        <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
