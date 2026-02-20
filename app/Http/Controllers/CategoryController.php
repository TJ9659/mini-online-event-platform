<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    //
    public function show(Request $request, Category $category)
    {
        $events = $category->events()->upcoming()->where('status', 'published')->when($request->sort, function ($query, $sort) {
            if ($sort === 'alp') {
                $query->orderBy('title', 'asc');
            } elseif ($sort === 'just_added') {
                $query->latest();
            } elseif ($sort === 'upcoming') {
                $query->orderBy('start_time', 'asc'); // sort by soonest first
            }
        }, function ($query) {
            $query->orderBy('start_time', 'asc'); // default sort
        })->paginate(9)->withQueryString();

        return Inertia::render('Category/Show', [
            'category' => $category,
            'events' => $events,
            'filters' => [
                'sort'   => $request->query('sort'),
            ]
        ]);
    }
    public function index()
    {
        return response()->json(Category::all());
    }
}
