<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::all();

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'price' => 'required|numeric|min:0',
            'seats' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
        } else {
            $path = null;
        }

        $event = Event::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'banner' => $path,
            'start_date' => $validatedData['start_date'],
            'price' => $validatedData['price'],
            'seats' => $validatedData['seats'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'event' => $event,
        ], 201);
    }

    public function edit($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        return response()->json(['event' => $event], 200);
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'price' => 'required|numeric',
            'seats' => 'required|integer|min:1',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $event->title = $request->input('title');
        $event->description = $request->input('description');
        $event->start_date = $request->input('start_date');
        $event->price = $request->input('price');
        $event->seats = $request->input('seats');

        if ($request->hasFile('banner')) {
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }

            $newBannerPath = $request->file('banner')->store('banners', 'public');
            $event->banner = $newBannerPath;
        }

        $event->save();

        return response()->json(['message' => 'Event updated successfully.', 'event' => $event], 200);
    }

    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.'], 200);
    }
}
