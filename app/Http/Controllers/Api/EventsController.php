<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('location')->orderBy('event_begin','desc')->get();
        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function shorts()
    {
        //return response()->json(Event::with('location')->where('event_begin','>',Carbon::now())->limit(3)->get());
        return response()->json(Event::with('location')->where('planning',false)->orderBy('event_begin','DESC')->limit(3)->get());
    }

    public function getEventImages()
    {
        if (!File::exists(storage_path('app/public/images'))) {
            File::makeDirectory(storage_path('app/public/images'));
        }
        if (!File::exists(storage_path('app/public/images/events'))) {
            File::makeDirectory(storage_path('app/public/images/events'));
        }

        $images = File::files(storage_path('app/public/images/events'));

        $imageDetails = array_map(function ($image) {
            return [
                'name' => $image->getFilename(),
                'url' => asset('storage/images/events/' . $image->getFilename()),
            ];
        }, $images);

        return response()->json($imageDetails);
    }

    public function register(Request $request,Event $event)
    {
        $validated = $request->validate(['user' => 'required|integer|exists:users,id']);
        $user = User::find($validated['user']);
        $crew = $user->hasRole(['Crew']);

        if ($crew) {
            if (!$event->attendingCrew->contains($user) && !$event->insiderCrew->contains($user) && !$event->registeredCrew->contains($user)) {
                $event->registeredCrew()->attach($user);
            }
        } else {
            if (!$event->attending->contains($user) && !$event->insider->contains($user) && !$event->registered->contains($user)) {
                $event->registered()->attach($user);
            }
        }
    }
}
