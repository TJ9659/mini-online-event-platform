<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //

    public function index()
    {
        $upcomingEvents = Event::upcoming()->where('status', 'published')->orderBy('start_time', 'asc')->take(6)->get();
        $featuredEvents = Event::upcoming()->where('status', 'published')->where('is_featured', true)->latest()->get();
        $categories = Category::all();
        return Inertia::render('Home', [
            'upcomingEvents' => $upcomingEvents,
            'featuredEvents' => $featuredEvents,
            'categories' => $categories,
        ]);
    }
}
