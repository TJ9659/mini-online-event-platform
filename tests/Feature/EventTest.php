<?php

use App\Mail\EventRegistered;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('Only upcoming events are visible', function () {
    // create one past event and one future event
    Event::factory()->create(['start_time' => now()->subDays(1)]);
    Event::factory()->create(['start_time' => now()->addDays(1)]);

    $response = $this->get(route('events.index'));

    // assert that only 1 event is passed to the Inertia page
    $response->assertInertia(
        fn($page) => $page
            ->has('events.data', 1)
    );
});

test('Reject if event capacity is reached', function () {
    $firstAttendee = User::factory()->create();
    $secondAttendee = User::factory()->create();
    $event = Event::factory()->create(['capacity' => 1]);

    Ticket::create(['status' => 'confirmed', 'event_id' => $event->id, 'user_id' => $firstAttendee->id]);


    $response = $this->actingAs($secondAttendee)
        ->post(route('tickets.store', $event));

    $this->assertEquals(1, Ticket::where('event_id', $event->id)->count());

    $response->assertSessionHasErrors(['registration']);
});

test("Don't allow edit if not organizer", function () {
    $organizer = User::factory()->create();
    $notTheOrganizer = User::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $response = $this->actingAs($notTheOrganizer)
        ->put(route('events.update', $event));

    $response->assertStatus(403);
});

test('Past events are not registrable', function () {
    $pastEvent = Event::factory()->create(['start_time' => now()->subDays(1)]);
    $attendee = User::factory()->create();

    Ticket::create(['status' => 'confirmed', 'event_id' => $pastEvent->id, 'user_id' => $attendee->id]);


    $response = $this->actingAs($attendee)
        ->post(route('tickets.store', $pastEvent));

    $response->assertSessionHasErrors(['registration']);
});

test('Event slug test', function () {
    $firstEvent = Event::factory()->create(['title' => 'title']);
    $secondEvent = Event::factory()->create(['title' => 'title']);

    $this->assertNotEquals($firstEvent->slug, $secondEvent->slug);
});


test('organizer can create an event with a valid image', function () {
    $this->seed(CategorySeeder::class);

    // grab the first category id from the database
    $categoryId = Category::first()->id;
    Storage::fake('public');

    $organizer = User::factory()->create();

    $image = UploadedFile::fake()->image('cover.png');

    $eventData = [
        'title' => 'Graphic Design Workshop',
        'description' => 'Learn how to make cool stuff.',
        'start_time' => now()->addDays(1)->toDateTimeString(),
        'end_time' => now()->addDays(1)->addHours(2)->toDateTimeString(),
        'capacity' => 20,
        'cover_image' => $image,
        'status' => 'published',
        'speaker' => 'Jane Smith',
        'platform_name' => 'Zoom',
        'meeting_link' => 'https://zoom.us/test',
        'organizer_id' => $organizer->id,
        'category_ids' => [$categoryId]
    ];

    $response = $this->actingAs($organizer)
        ->post(route('events.store'), $eventData);

    $response->assertRedirect();

    // $response->dumpSession();

    $this->assertDatabaseHas('events', ['title' => 'Graphic Design Workshop']);

    $event = Event::where('title', 'Graphic Design Workshop')->first();
    Storage::disk('public')->assertExists($event->cover_image);
});

test('organizer can update image and old image is deleted', function () {
    Storage::fake('public');
    $organizer = User::factory()->create();

    $oldPath = 'events/old_cover.png';
    Storage::disk('public')->put($oldPath, 'fake content');

    $event = Event::factory()->create([
        'title' => 'Graphic Design Workshop',
        'organizer_id' => $organizer->id,
        'cover_image' => $oldPath,
        'status' => 'draft'
    ]);

    $newImage = UploadedFile::fake()->image('new_cover.png');

    $response = $this->actingAs($organizer)
        ->put(route('events.update', $event), [
            'title' => 'Graphic Design Workshop',
            'status' => 'draft',
            'cover_image' => $newImage,
        ]);

    $response->assertRedirect();
    // $response->dumpSession();

    Storage::disk('public')->assertMissing($oldPath);

    $event->refresh();
    Storage::disk('public')->assertExists($event->cover_image);
});

test("Fail if event's end-time is earlier than start-time", function () {
    $this->seed(CategorySeeder::class);
    $organizer = User::factory()->create();
    $categoryId = Category::first()->id;
    $start = now()->addDays(2);
    $end = $start->copy()->subHour(1);


    $eventData = [
        'title' => 'Graphic Design Workshop',
        'description' => 'Learn how to make cool stuff.',
        'start_time' => $start,
        'end_time' => $end,
        'capacity' => 20,
        'cover_image' => UploadedFile::fake()->image('cover.png'),
        'status' => 'published',
        'speaker' => 'Jane Smith',
        'platform_name' => 'Zoom',
        'meeting_link' => 'https://zoom.us/test',
        'organizer_id' => $organizer->id,
        'category_ids' => [$categoryId]
    ];

    $response = $this->actingAs($organizer)
        ->post(route('events.store'), $eventData);

    $response->assertSessionHasErrors(['end_time']);
});


test("Fail if user registered for the event twice", function () {
    $attendee = User::factory()->create();
    $event = Event::factory()->create();

    $this->actingAs($attendee)->post(route('tickets.store', $event));
    $response = $this->actingAs($attendee)->post(route('tickets.store', $event));

    $response->assertSessionHasErrors(['registration']);
});

test("The Home Page and Event Page only show events that are published", function () {
    Event::factory()->create(['title' => 'Published Event', 'status' => 'published']);
    Event::factory()->create(['title' => 'Draft Event', 'status' => 'draft']);

    $eventsIndexResponse = $this->get(route('events.index'));

    $homeResponse = $this->get(route('home'));


    $homeResponse->assertSee('Published Event');
    $homeResponse->assertDontSee('Draft Event');

    $eventsIndexResponse->assertSee('Published Event');
    $eventsIndexResponse->assertDontSee('Draft Event');
});

test("Returns events in chronological order through upcoming scope", function () {
    $farFuture = Event::factory()->create(['start_time' => now()->addDays(10), 'title' => 'Far Event']);
    $nearFuture = Event::factory()->create(['start_time' => now()->addDays(2), 'title' => 'Soon Event']);
    $middleFuture = Event::factory()->create(['start_time' => now()->addDays(5), 'title' => 'Middle Event']);

    $response = $this->get(route('events.index', [
        'filters' => [
            'sort' => 'upcoming'
        ]
    ]));

    $response->assertInertia(
        fn(Assert $page) => $page
            ->component('Event/Index')
            ->has('events.data', 3)
            ->where('events.data.0.id', $nearFuture->id)
            ->where('events.data.1.id', $middleFuture->id)
            ->where('events.data.2.id', $farFuture->id)
    );
});


test("Deleting an event will delete the tickets associated", function () {
    $organizer = User::factory()->create();
    $attendee = User::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $ticket = Ticket::create(['status' => 'confirmed', 'event_id' => $event->id, 'user_id' => $attendee->id]);

    $this->actingAs($attendee)
        ->get(route('tickets.index'))
        ->assertSee('confirmed');

    $response = $this->actingAs($organizer)
        ->delete(route('events.destroy', $event));

    $response->assertRedirect();

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});


test("Searching an event title will return the result", function () {
    $targetEvent = Event::factory()->create(['title' => 'Target Event']);
    $otherEvent = Event::factory()->create(['title' => 'Other Event']);

    $response = $this->get(route('events.index', ['search' => 'Target']));

    $response->dumpSession();

    $response->assertInertia(
        fn(Assert $page) => $page
            ->component('Event/Index')
            ->has('events.data', 1)
            ->where('events.data.0.id', $targetEvent->id)
            ->whereNot('events.data.0.id', $otherEvent->id)
            ->where('filters.search', 'Target')
    );
});


test("it sends an email with the meeting link after registration", function () {
    Mail::fake();

    $event = Event::factory()->create(['meeting_link' => 'https://zoom.us/j/123']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('tickets.store', $event));

    // assert the mail was sent
    Mail::assertSent(EventRegistered::class, function ($mail) use ($user, $event) {
        return $mail->hasTo($user->email) &&
            $mail->event->id === $event->id;
    });
});

test("the registration email contains the meeting link", function () {
    Mail::fake();

    $event = Event::factory()->create([
        'meeting_link' => 'https://zoom.us/j/my-secret-meeting'
    ]);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('tickets.store', $event));

    Mail::assertSent(EventRegistered::class, function ($mail) {
        // this renders the Markdown into a string so we can search it
        $html = $mail->render();

        return str_contains($html, 'https://zoom.us/j/my-secret-meeting') &&
            str_contains($html, 'Join Event'); // Also check for your button text
    });
});
