@extends('layouts.app')

@section('content')

<style>
    /* Style untuk membungkus kalender */
    #calendar {
        max-width: 80%;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style tambahan untuk form */
    .form-container {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style header FullCalendar (judul dan navigasi) */
    .fc-toolbar {
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        border-radius: 8px 8px 0 0;
    }

    .fc-toolbar h2 {
        font-size: 1.5rem;
        color: #ffffff;
    }

    .fc-button-group .fc-button {
        background-color: #ffffff;
        color: #007bff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .fc-button-group .fc-button:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    .fc-button-primary {
        background-color: #ffffff;
        color: #007bff;
        border: none;
    }

    .fc-button-primary:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    /* Style untuk grid hari */
    .fc-daygrid-day {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    /* Style untuk hari saat ini */
    .fc-day-today {
        background-color: #007bff !important;
        color: white;
    }

    /* Hover effect pada tanggal */
    .fc-daygrid-day:hover {
        background-color: #007bff;
        color: #ffffff;
        transition: background-color 0.3s ease;
    }

    /* Style untuk event */
    .fc-event {
        background-color: #28a745;
        color: white;
        border-radius: 5px;
        border: none;
        padding: 5px;
        font-size: 0.9rem;
    }

    /* Style pada header grid */
    .fc-col-header-cell {
        background-color: #007bff;
        color: white;
        padding: 10px;
    }

    .fc-daygrid-day-number {
        padding: 8px;
        font-weight: bold;
        color: #007bff;
    }
</style>

<div class="container">
    <div class="row">
        <!-- Bagian Kiri: Input Biodata -->
        <div class="col-md-5">
            <div class="form-container p-4 mb-4">
                <h2 class="mb-4">Biodata</h2>
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name" class="font-weight-bold">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="font-weight-bold">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nip" class="font-weight-bold">NIP</label>
                        <input type="text" class="form-control" name="nip" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="department" class="font-weight-bold">Departemen</label>
                        <input type="text" class="form-control" name="department" required>
                    </div>
                
            </div>
        </div>

        <!-- Bagian Kanan: Kalender -->
        <div class="col-md-7">
            <div id="calendar" class="shadow-sm rounded" style="background-color: #f8f9fa;"></div>
            </div>
    </div>
</div>

<!-- Modal untuk form booking -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingModalLabel">Book Meeting Room</h5>
            </div>
            
            <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="selectedDate" name="date">

                    <div class="form-group">
                        <label for="room" class="font-weight-bold">Select Room</label>
                        <select name="room_id" class="form-control" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_time" class="font-weight-bold">Start Time</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="end_time" class="font-weight-bold">End Time</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="font-weight-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk mengedit event -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Event</h5>
            </div>
            
            <form id="editEventForm" action="{{ route('bookings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="editEventId" name="event_id">

                <div class="modal-body">
                    <!-- Input Judul Event -->
                    <div class="form-group">
                        <label for="editTitle" class="font-weight-bold">Event Title</label>
                        <input type="text" id="editTitle" name="title" class="form-control" required>
                    </div>

                    <!-- Input Tanggal Mulai dan Selesai -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="editStartDate" class="font-weight-bold">Start Date</label>
                            <input type="datetime-local" id="editStartDate" name="start_date" class="form-control" >
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editEndDate" class="font-weight-bold">End Date</label>
                            <input type="datetime-local" id="editEndDate" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
            <div class="row mx-2">
            <form id="deleteEventForm" action="{{ route('bookings.delete') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteEventId" name="event_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
            </div>
        </div>
    </div>
</div>


@endsection

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.4.2/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var events = @json($eventsz); // Ambil event dari server-side

    var formattedEvents = events.map(function(event) {
        return {
            id: event.id, // Menyimpan ID event Google Calendar
            title: event.summary,
            start: event.start.dateTime || event.start.date, // Support untuk event tanpa waktu (all-day)
            end: event.end.dateTime || event.end.date
        };
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid', 'interaction'],
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        initialView: 'dayGridMonth',
        events: formattedEvents, // Event dari Google Calendar

        // Event click handler untuk mengedit event
        eventClick: function(info) {
            var eventId = info.event.id; // Mendapatkan ID event

            // Mengisi data di modal edit
            $('#editEventId').val(eventId);
            $('#editTitle').val(info.event.title);
            $('#editStartDate').val(info.event.startStr);
            $('#editEndDate').val(info.event.endStr);
            $('#deleteEventId').val(eventId);

            // Tampilkan modal edit
            $('#editModal').modal('show');
        },

        dateClick: function(info) {
            // Tampilkan modal untuk membuat booking baru
            $('#selectedDate').val(info.dateStr);
            $('#bookingModal').modal('show');
        }
    });

    calendar.render();
});
</script>
