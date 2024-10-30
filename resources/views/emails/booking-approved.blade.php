<!DOCTYPE html>
<html>
<head>
    <title>Booking Approved</title>
</head>
<body>
    <h1>Meeting Room Booking Approved</h1>
    <p>Hi {{ $booking->user->name }},</p>
    <p>Your booking for the room <strong>{{ $booking->room->name }}</strong> on <strong>{{ $booking->date }}</strong> has been approved.</p>
    <p>Booking details:</p>
    <ul>
        <li>Name: {{ $booking->nama }}</li>
        <li>Email: {{ $booking->email }}</li>
        <li>NIP: {{ $booking->nip }}</li>
        <li>Department: {{ $booking->department }}</li>
        <li>Date: {{ $booking->date }}</li>
        <li>Start Time: {{ $booking->start_time }}</li>
        <li>End Time: {{ $booking->end_time }}</li>
        <li>Room: {{ $booking->room->name }}</li>
    </ul>
    <p>Thank you for using our service!</p>
</body>
</html>
