<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        $start_time = now()->addDays(rand(1, 30));
        $end_time = $start_time->copy()->addHours(rand(1, 6)); // to ensure accurate date time 
        return [
            'organizer_id' => User::factory(), // used for test purposes to ensure user exist when running tests
            'title' => $title,
            'slug' => str()->slug($title) . '-' . str()->random(5),
            'description' => $this->faker->paragraphs(3, true),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'platform_name' => 'Zoom',
            'meeting_link' => 'https://zoom.us/j/test',
            'capacity' => rand(10, 100),
            'status' => 'published',
            'speaker' => 'Mr. John Doe'
        ];
    }
}
