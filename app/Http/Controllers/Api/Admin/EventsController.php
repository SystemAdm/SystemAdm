<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventsController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function unCancel(Request $request, Event $event){
        $event->cancelled_time = null;
        $event->cancelled_text = null;
        $event->cancelled_by = null;
        $event->save();
        return response()->json(['success' => 'Event uncancelled successfully']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Event::withTrashed()->with('location', 'registered', 'registeredCrew', 'insider', 'insiderCrew', 'attending', 'attendingCrew')->orderBy('event_begin', 'desc')->get();
    }

    public function recover(string $event)
    {
        $recover = Event::withTrashed()->where('id', $event)->first();
        $recover->restore();
        return response()->json(['success' => 'Event recovered successfully']);
    }

    public function startOver(Request $request, Event $event)
    {
        $validated = $request->validate([
            'showEvent' => ['required', 'boolean'],
            'showSignup' => ['required', 'boolean'],
            'eventBegin' => ['required_if:showEvent,true', 'date', 'nullable'],
            'eventEnd' => ['required_if:showEvent,true', 'date', 'nullable'],
            'signupBegin' => ['required_if:showSignup,true', 'date', 'nullable'],
            'signupEnd' => ['required_if:showSignup,true', 'date', 'nullable'],
        ]);

        if ($validated['showEvent']) {
            $event->event_begin = $validated['eventBegin'];
            $event->event_end = $validated['eventEnd'];
        }
        if ($validated['showSignup']) {
            $event->signup_begin = $validated['signupBegin'];
            $event->signup_end = $validated['signupEnd'];
        }

        $event->save();
        return response()->json(['success' => 'Event updated successfully']);
    }

    public function eventBeginNow(Event $event)
    {
        $event->event_begin = now();
        $event->save();
        return response()->json(['success' => 'Event updated successfully']);
    }

    public function eventEndNow(Event $event)
    {
        $event->event_end = now();
        $event->save();
        return response()->json(['success' => 'Event updated successfully']);
    }

    public function signupBeginNow(Event $event)
    {
        $event->signup_begin = now();
        $event->save();
        return response()->json(['success' => 'Event updated successfully']);
    }

    public function signupEndNow(Event $event)
    {
        $event->signup_end = now();
        $event->save();
        return response()->json(['success' => 'Event updated successfully']);
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
            'location' => ['required', 'exists:locations,id'],
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
    public function show(int $id)
    {
        return Event::withTrashed()->with('cancelled','location', 'registered', 'registeredCrew', 'insider', 'insiderCrew', 'attending', 'attendingCrew')->findOrFail($id);
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
        $event = Event::withTrashed()->find($id);
        $event->delete();
        return response()->json(['success' => 'Event deleted successfully']);
    }

    public function permanent(string $event)
    {
        $event = Event::withTrashed()->find($event);
        $event->forceDelete();
        return response()->json(['success' => 'Event deleted successfully']);
    }

    public function cancel(Request $request,string $event)
    {
        $validated = $request->validate([
            'cancelled_text' => ['nullable', 'string', 'min:3', 'max:2048'],
            'notify_crew' => ['nullable', 'boolean'],
            'notify_guests' => ['nullable', 'boolean'],
        ]);
        $event = Event::withTrashed()->find($event);
        $event->cancelled_time = now();
        $event->cancelled_text = $validated['cancelled_text'];
        $event->cancelled_by = auth()?->user()?->id ?? 8;
        $event->save();
        return response()->json(['success' => 'Event cancelled successfully','event'=>$event]);
    }

    public function register(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Sjekk om brukeren er Crew
        if ($user->hasRole('Crew')) {
            // Legg brukeren til Registered Crew hvis ikke allerede registrert
            if (!$event->registeredCrew()->where('user_id', $user->id)->exists()) {
                $event->registeredCrew()->attach($user->id);
            }

            // Logg og returner respons
            Log::info("User {$user->id} has been registered as crew for event {$event->id}.");

            return response()->json([
                'success' => 'User is now registered to the event as a crew member.',
                'action' => 'register',
                'user' => $user->id,
            ]);
        } else {
            // Legg brukeren til Registered hvis ikke allerede registrert
            if (!$event->registered()->where('user_id', $user->id)->exists()) {
                $event->registered()->attach($user->id);
            }

            // Logg og returner respons
            Log::info("User {$user->id} has been registered as a guest for event {$event->id}.");

            return response()->json([
                'success' => 'User is now registered to the event.',
                'action' => 'register',
                'user' => $user->id,
            ]);
        }
    }

    public function unregister(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Håndter fjerning basert på brukers rolle
        if ($user->hasRole('Crew')) {
            // Logg og returner respons for Crew
            Log::info("User {$user->id} has been removed as crew from event {$event->id}.");
            return response()->json([
                'success' => 'User is now removed from the event as a crew member.',
                'action' => 'unregisterCrew',
                'user' => $user->id,
            ]);
        } else {
            // Fjern brukeren fra relasjoner som gjelder vanlige brukere
            $this->detachRelations($event, $user, ['registered', 'attending', 'insider', 'attendingCrew', 'registeredCrew', 'insiderCrew']);

            // Logg og returner respons for vanlig bruker
            Log::info("User {$user->id} has been removed as a guest from event {$event->id}.");
            return response()->json([
                'success' => 'User is now removed from the event.',
                'action' => 'unregister',
                'user' => $user->id,
            ]);
        }
    }


    public function attend(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Håndter bruker basert på rolle
        if ($user->hasRole('Crew')) {
            // Legg brukeren til attendingCrew
            if (!$event->attendingCrew()->where('user_id', $user->id)->exists()) {
                $event->attendingCrew()->attach($user->id);
            }

            // Returner respons for Crew
            return response()->json([
                'success' => 'User is now attending the event as a crew member.',
                'action' => 'attendCrew',
                'user' => $user,
            ]);
        } else {
            // Legg brukeren til attending
            if (!$event->attending()->where('user_id', $user->id)->exists()) {
                $event->attending()->attach($user->id);
            }

            // Returner respons for vanlig bruker
            return response()->json([
                'success' => 'User is now attending the event.',
                'action' => 'attend',
                'user' => $user,
            ]);
        }
    }

    public function unattend(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Håndter bruker basert på rolle
        if ($user->hasRole('Crew')) {
            // Legg brukeren til i 'registeredCrew', om nødvendig
            if (!$event->registeredCrew()->where('user_id', $user->id)->exists()) {
                $event->registeredCrew()->attach($user->id);
            }

            // Returner respons for Crew
            return response()->json([
                'success' => 'User is now registered to the event as a crew member.',
                'action' => 'unattendCrew',
                'user' => $user->id,
            ]);
        } else {
            // Legg brukeren til i 'registered', om nødvendig
            if (!$event->registered()->where('user_id', $user->id)->exists()) {
                $event->registered()->attach($user->id);
            }

            // Returner respons for vanlig bruker
            return response()->json([
                'success' => 'User is now registered to the event.',
                'action' => 'unattend',
                'user' => $user->id,
            ]);
        }
    }

    public function inside(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Håndter bruker basert på rolle
        if ($user->hasRole('Crew')) {
            // Legg brukeren til i 'insiderCrew', om nødvendig
            if (!$event->insiderCrew()->where('user_id', $user->id)->exists()) {
                $event->insiderCrew()->attach($user->id);
            }

            // Returner respons for Crew
            return response()->json([
                'success' => 'User is now inside the event as a crew member.',
                'action' => 'insiderCrew',
                'user' => $user->id,
            ]);
        } else {
            // Legg brukeren til i 'insider', om nødvendig
            if (!$event->insider()->where('user_id', $user->id)->exists()) {
                $event->insider()->attach($user->id);
            }

            // Returner respons for vanlig bruker
            return response()->json([
                'success' => 'User is now inside the event.',
                'action' => 'insider',
                'user' => $user->id,
            ]);
        }
    }

    public function leave(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);

        // Fjern brukeren fra relasjonene Crew bruker
        $this->detachRelations($event, $user, [Event::RELATIONS['ATTENDING'], Event::RELATIONS['ATTENDING_CREW'], Event::RELATIONS['INSIDER'], Event::RELATIONS['INSIDER_CREW'], Event::RELATIONS['REGISTERED'], Event::RELATIONS['REGISTERED_CREW']]);

        // Håndter bruker basert på rolle
        if ($user->hasRole('Crew')) {
            // Legg brukeren til i 'registeredCrew', om nødvendig
            if (!$event->AttendingCrew()->where('user_id', $user->id)->exists()) {
                $event->AttendingCrew()->attach($user->id);
            }

            // Returner respons for Crew
            return response()->json([
                'success' => 'User has now left the event as a crew member.',
                'action' => 'leaveCrew',
                'user' => $user->id,
            ]);
        } else {
            // Fjern brukeren fra relevante vanlige brukerrelasjoner
            $this->detachRelations($event, $user, ['insider', 'insiderCrew']);

            // Legg brukeren til i 'registered', om nødvendig
            if (!$event->Attending()->where('user_id', $user->id)->exists()) {
                $event->Attending()->attach($user->id);
            }

            // Returner respons for vanlig bruker
            return response()->json([
                'success' => 'User has now left the event.',
                'action' => 'leave',
                'user' => $user->id,
            ]);
        }
    }

    public function trash(Request $request, Event $event)
    {
        $user = $this->validateAndGetUser($request);
        $this->detachRelations($event, $user, [
            Event::RELATIONS['ATTENDING'],
            Event::RELATIONS['ATTENDING_CREW'],
            Event::RELATIONS['INSIDER'],
            Event::RELATIONS['INSIDER_CREW'],
            Event::RELATIONS['REGISTERED'],
            Event::RELATIONS['REGISTERED_CREW']
        ]);
        return response()->json(['success' => 'Event trashed successfully.', 'action' => 'trash', 'user' => $user->id]);
    }

    /**
     * Fjern brukeren fra spesifikke relasjoner på arrangementet.
     *
     * @param Event $event
     * @param User $user
     * @param array $relations
     * @return void
     */
    protected function detachRelations(Event $event, User $user, array $relations)
    {
        foreach ($relations as $relation) {
            if ($event->$relation()->where('user_id', $user->id)->exists()) {
                $event->$relation()->detach($user->id);
            }
        }
    }

    /**
     * Valider bruker-ID og hent brukerobjektet.
     *
     * @param Request $request
     * @return User
     */
    protected function validateAndGetUser(Request $request): User
    {
        $validated = $request->validate(['user' => ['required', 'exists:users,id']]);
        return User::findOrFail($validated['user']);
    }
}
