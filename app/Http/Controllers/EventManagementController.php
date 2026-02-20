<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class EventManagementController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $events = auth()->user()->organizedEvents()->orderBy('start_time', 'asc')->when($request->input('search'), function ($query, $search) {
            $query->where('title', 'LIKE', "%{$search}%");
        })->when(
            $request->sort === 'past',
            fn($query) => $query->past(),
            fn($query) => $query->upcoming()
        )->paginate(9)
            ->withQueryString();


        return Inertia::render('Manage/Events/Index', [
            'events' => $events,
            'filters' => [
                'search' => $request->query('search'),
                'sort' => $request->query('sort')
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Manage/Events/Create', [
            'availableCategories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $isPublished = $request->status === 'published';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status'        => 'required|in:draft,published',
            'cover_image'   => [$isPublished ? 'required' : 'nullable', 'image'],
            // below are only required if status is published
            'description'   => $isPublished ? 'required|string' : 'nullable|string',
            'platform_name' => $isPublished ? 'required|string' : 'nullable|string',
            'meeting_link'  => $isPublished ? 'required|url' : 'nullable|url',
            'start_time'    => $isPublished ? 'required|date|after:now' : 'nullable|date',
            'end_time'      => $isPublished ? 'required|date|after:start_time' : 'nullable|date',
            'capacity'      => $isPublished ? 'required|integer|min:1' : 'nullable|integer',
            'speaker'       => $isPublished ? 'required|string' : 'nullable|string',
            'category_ids'  => $isPublished ? 'required|array|min:1' : 'nullable|array',
        ]);



        $categoryIds = $request->input('category_ids', []);

        if ($request->hasFile('cover_image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . 'webp';
            $manager = new ImageManager(Driver::class);

            $encoded = $manager->read($request->file('cover_image'))
                ->scale(width: 1200)
                ->toWebp(75);

            Storage::disk('public')->put('events/' . $imageName, $encoded);

            $validated['cover_image'] = '/events/' . $imageName;
        }

        $dataToSave = array_merge($validated, [
            'slug' => Str::slug($request->title) . '-' . strtolower(Str::random(5)),
        ]);

        $event = auth()->user()->organizedEvents()->create(
            Arr::except($dataToSave, ['category_ids'])
        );

        if (!empty($categoryIds)) {
            $event->categories()->sync($categoryIds);
        }



        if ($isPublished) {
            return redirect()->route('events.show', $event)->with('message', 'Event created successfully!');
        }
        return redirect()->route('events.draft')->with('message', 'Event draft saved!');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $event->load(['organizer', 'categories'])->makeVisible(['meeting_link']);

        $availableCategories = Category::all();


        return Inertia::render('Manage/Events/Edit', [
            'event' => $event,
            'availableCategories' => $availableCategories,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $isPublished = $request->status === 'published';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status'        => 'required|in:draft,published',
            'cover_image'   => [($isPublished && !$event->cover_image) ? 'required' : 'nullable', 'image'],
            // below are only required if status is published
            'description'   => $isPublished ? 'required|string' : 'nullable|string',
            'platform_name' => $isPublished ? 'required|string' : 'nullable|string',
            'meeting_link'  => $isPublished ? 'required|url' : 'nullable|url',
            'start_time'    => $isPublished ? 'required|date|after:now' : 'nullable|date',
            'end_time'      => $isPublished ? 'required|date|after:start_time' : 'nullable|date',
            'capacity'      => $isPublished ? 'required|integer|min:1' : 'nullable|integer',
            'speaker'       => $isPublished ? 'required|string' : 'nullable|string',
            'category_ids'  => $isPublished ? 'required|array|min:1' : 'nullable|array',
        ]);

        $categoryIds = $request->input('category_ids', []);

        if ($request->hasFile('cover_image')) {

            $oldFile = $event->getRawOriginal('cover_image');

            if ($oldFile) {
                Storage::disk('public')->delete($oldFile);
            }

            $imageName = time() . '_' . Str::random(10) . '.' . 'webp';

            $manager = new ImageManager(Driver::class);
            $encoded = $manager->read($request->file('cover_image'))
                ->scale(width: 1200)
                ->toWebp(75);

            Storage::disk('public')->put('events/' . $imageName, $encoded);
            $validated['cover_image'] = '/events/' . $imageName;
        } else {
            unset($validated['cover_image']);
        }

        $dataToUpdate = Arr::except($validated, ['category_ids']);

        if ($event->title !== $validated['title']) {
            $dataToUpdate['slug'] = Str::slug($validated['title']) . '-' . strtolower(Str::random(5));
        }

        $event->update($dataToUpdate);

        $categoryIds = $request->input('category_ids', []);
        $event->categories()->sync($categoryIds);

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Event updated successfully!');
    }

    public function draft(Request $request)
    {
        $events = auth()->user()->organizedEvents()->where('status', 'draft')->when($request->input('search'), function ($query, $search) {
            $query->where('title', 'LIKE', "%{$search}%");
        })->paginate(9)
            ->withQueryString();

        return Inertia::render('Manage/Events/Draft', [
            'events' => $events,
            'filters' => $request->only(['search']) // Useful to keep the search box filled
        ]);
    }

    public function destroy(Event $event)
    {
        $event->status === 'published';
        $event->tickets()->delete();
        $event->delete();

        return back()->with('message', 'Event deleted!.');
    }

    protected function deleteOldImage($filename)
    {
        if ($filename && $filename !== 'default-event-banner.png') {
            @unlink(public_path('storage/events/' . $filename));
        }
    }
}
