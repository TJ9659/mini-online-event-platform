<?php

namespace App\Http\Controllers;

use App\Mail\EventRegistered;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TicketController extends Controller
{
    //
    public function store(Event $event)
    {
        $currentRegistration = $event->tickets()->count();

        if ($event->isFull()) {
            return back()->withErrors([
                'registration' => 'Sorry, this event has reached its maximum capacity.'
            ]);
        }

        if ($event->isPast()) {
            return back()->withErrors([
                'registration' => 'Sorry, this event has occured.'
            ]);
        }

        $hasTicket = auth()->check() && $event->tickets()->where('user_id', auth()->id())->exists();

        if ($hasTicket) {
            return back()->withErrors([
                'registration' => 'You have already registered for this event!'
            ]);
        }

        Ticket::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);

        Mail::to(auth()->user())->send(new EventRegistered($event));

        return redirect()->route('events.show', $event)->with('message', 'Registration successful!');
    }

    public function index()
    {

        $tickets = Ticket::with('event')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(9);

        // to iterate through the tickets to find if meeting_link should be visible based on cancelled or not
        $tickets->getCollection()->each(function ($ticket) {
            if ($ticket->status !== 'cancelled' && $ticket->event) {
                $ticket->event->makeVisible('meeting_link');
            }
        });

        return Inertia::render('Manage/Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            return back()->with('message', 'Unauthorized action');
        }

        $ticket->delete();

        return back()->with('message', 'Succesfully deleted ticket.');
    }

    public function cancel(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            return back()->with('message', 'Unauthorized action');
        }

        $ticket->status = 'cancelled';
        $ticket->save();

        return back()->with('message', 'Succesfully cancelled ticket.');
    }
}
