<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Event::all()->reverse());
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
        return response()->json(Event::with('location')->where('event_begin','>',Carbon::now())->limit(3)->get());
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
        $imageUrls = array_map(function ($image) {
            return asset('storage/images/events/' . $image->getFilename());
        }, $images);

        return response()->json($imageUrls);
    }
}
