<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EventController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $events = Event::query()
            ->with('categories')
            ->where('status', 'published')
            ->upcoming()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('title', 'LIKE', "%{$search}%");
            })
            ->when($request->input('sort'), function ($query, $sort) {
                if ($sort === 'alp') {
                    $query->orderBy('title', 'asc');
                } elseif ($sort === 'just_added') {
                    $query->latest();
                } elseif ($sort === 'upcoming') {
                    $query->orderBy('start_time', 'asc'); // Sort by soonest first
                }
            }, function ($query) {
                $query->orderBy('start_time', 'asc');
            })
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Event/Index', [
            'events' => $events,
            'filters' => [
                'search' => $request->query('search'),
                'sort' => $request->query('sort')
            ]
        ]);
    }

    public function show(Event $event)
    {
        if ($event->start_time < now() && auth()->id() !== $event->organizer_id) {
            abort(404, 'This event has already taken place.');
        }

        $event->load(['organizer', 'categories']);

        $isEventFull = $event->isFull();

        $categoryIds = $event->categories->pluck('id');

        $userTicket = auth()->check()
            ? $event->tickets()->where('user_id', auth()->id())->first()
            : null;

        $isOrganizer = auth()->id() === $event->organizer_id;
        $hasActiveTicket = $userTicket && $userTicket->status !== 'cancelled';

        if ($isOrganizer || $hasActiveTicket) {
            $event->makeVisible('meeting_link');
        } else {
            $event->makeHidden('meeting_link');
        }
        $similarEvents = Event::with('categories')
            ->where('id', '!=', $event->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->latest()
            ->take(4)
            ->get();

        if ($isOrganizer || $hasActiveTicket) {
            $event->makeVisible('meeting_link');
        } else {
            $event->makeHidden('meeting_link');
        }

        return Inertia::render('Event/Show', [
            'event' => $event,
            'hasTicket' => $hasActiveTicket,
            'ticketStatus' => $userTicket?->status,
            'isOrganizer' => $isOrganizer,
            'similarEvents' => $similarEvents,
            'isEventFull' => $isEventFull,
        ]);
    }

    public function registrationsIndex(Request $request)
    {
        $events = auth()->user()->organizedEvents()->where('status', 'published')->when($request->input('search'), function ($query, $search) {
            $query->where('title', 'LIKE', "%{$search}%");
        })->with('tickets.user:id,name,email')->withCount('tickets') // Tells you how many people signed up
            ->latest()->paginate(9)
            ->withQueryString();

        return Inertia::render('Manage/Registrations/Index', [
            'events' => $events,
            'filters' => $request->only(['search'])
        ]);
    }
}
