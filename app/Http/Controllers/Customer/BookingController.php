<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {

        $userId = $request->user()->id;

        $events = Event::with(['bookings' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
            ->get()
            ->map(function ($event) use ($userId) {
                $totalQuantity = $event->bookings->where('user_id', $userId)->sum('quantity');
                $event->total_booked_quantity = $totalQuantity;
                return $event;
            });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($request, $userId) {
            $event = Event::findOrFail($request->event_id);

            // Lock the row for this event to prevent race conditions
            $totalBookedSeats = Booking::where('event_id', $event->id)
                ->lockForUpdate() 
                ->sum('quantity');
            $availableSeats = $event->seats - $totalBookedSeats;

            if ($request->quantity > $availableSeats) {
                return response()->json(['message' => 'Not enough seats available.'], 400);
            }
            $booking = Booking::create([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'quantity' => $request->quantity,
            ]);

            Mail::to(Auth::user()->email)->send(new BookingConfirmationMail($booking, $event));

            return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
        });
    }
}
