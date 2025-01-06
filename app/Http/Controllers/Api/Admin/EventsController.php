<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Event::withTrashed()->with('location')->orderBy('event_begin', 'desc')->where('id',122)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event.begin' => ['required', 'date'],
            'event.end' => ['required', 'date'],
            'event.registration' => ['required', 'integer', 'min:0', 'max:2'],

            'registration.begin_date' => ['nullable', 'date'],
            'registration.begin' => ['required', 'integer', 'min:0', 'max:2'],
            'registration.end_date' => ['nullable', 'date'],
            'registration.end' => ['required', 'integer', 'min:0', 'max:2'],

            'restriction.type' => ['required', 'string', 'min:3', 'max:5'],
            'restriction.min.age' => ['required_if:restriction.type,age'],
            'restriction.limit.age' => ['required', 'boolean'],
            'restriction.max.age' => ['required_if:restriction.limit.age,true'],
            'restriction.esrb' => ['required_if:restriction.type,esrb'],
            'restriction.pegi' => ['required_if:restriction.type,pegi'],
            'restriction.min.grade' => ['required_if:restriction.type,grade'],
            'restriction.limit.grade' => ['required', 'boolean'],
            'restriction.max.grade' => ['required_if:restriction.limit.grade,true'],

            'attending' => ['required', 'integer', 'min:0', 'max:2'],
            'description' => ['required', 'string', 'min:3', 'max:2048'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'seats' => ['required', 'integer', 'min:-1', 'max:255'],
            'image' => ['nullable', 'string'],
            'location' => ['required','exists:locations,id'],
        ]);

        $event = new Event([
            'event_begin' => $validated['event']['begin'],
            'event_end' => $validated['event']['end'],
            'title' => $validated['title'],
            'seats' => $validated['seats'],
            'description' => $validated['description'],
            'image' => $validated['image'],
            'registration_needed' => $validated['event']['registration'],
            'location_id' => $validated['location'],
        ]);

        switch ($validated['attending']) {
            case 0:
            {
                $event->public = true;
                break;
            }
            case 1:
            {
                $event->protected = true;
                break;
            }
            case 2:
            {
                $event->private = true;
                break;
            }
        }

        if ($validated['event']['registration'] != 0) {
            $event->signup_begin = match ($validated['registration']['begin']) {
                1 => now(),
                2 => $validated['event']['begin'],
                default => $validated['registration']['begin_date'],
            };

            $event->signup_end = match ($validated['registration']['end']) {
                1 => $validated['event']['begin'],
                2 => $validated['event']['end'],
                default => $validated['registration']['end_date'],
            };
        }

        switch ($validated['restriction']['type']) {
            case 'pegi':
                $event->rating = 'pegi';
                $event->rating_min = $validated['restriction']['pegi'];
                $event->rating_max = null;
                break;

            case 'esrb':
                $event->rating = 'esrb';
                $event->rating_min = $validated['restriction']['esrb'];
                $event->rating_max = null;
                break;

            case 'age':
                $event->rating = 'age';
                $event->rating_min = $validated['restriction']['min']['age'];
                $event->rating_max = $validated['restriction']['limit']['age'] ? $validated['restriction']['max']['age'] : null;
                break;

            case 'grade':
                $event->rating = 'grade';
                $event->rating_min = $validated['restriction']['min']['grade'];
                $event->rating_max = $validated['restriction']['limit']['grade'] ? $validated['restriction']['max']['grade'] : null;
                break;
        }

        $event->save();

        return response()->json(['success' => 'Event created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
