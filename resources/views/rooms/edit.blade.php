@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Card untuk form edit -->
            <div class="card">
                <div class="card-header">
                    <h4>Edit Room</h4>
                </div>
                <div class="card-body">
                    <!-- Tampilkan pesan error jika ada -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form untuk update room -->
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Input Nama Ruangan -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Room Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $room->name }}" required>
                        </div>

                        <!-- Tombol Submit dan Cancel -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
