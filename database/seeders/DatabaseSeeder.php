<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('events');
        Storage::disk('public')->makeDirectory('events');
        $this->call(CategorySeeder::class);
        User::factory(40)->create();

        $users = User::all();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = Category::all();

        $events = Event::factory(60)->create([
            'organizer_id' => $user->id,
        ])->each(function ($event) use ($categories) {
            $uniqueName = "event_{$event->id}.png";
            $path = "events/{$uniqueName}";

            File::copy(
                resource_path('images/default-event-banner.png'),
                storage_path("app/public/{$path}")
            );

            $event->update(['cover_image' => $path]);
            $event->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );
        });

        $events->each(function ($event) use ($users) {

            $eligibleUsers = $users->reject(fn($user) => $user->id === $event->organizer_id);
            $attendees = $eligibleUsers->random(rand(5, 20));

            foreach ($attendees as $attendee) {
                Ticket::create([
                    'user_id'  => $attendee->id,
                    'event_id' => $event->id,
                    'status' => 'confirmed'
                ]);
            }
        });

        Event::whereIn('id', $events->random(3)->pluck('id'))
            ->update(['is_featured' => true]);
    }
}
