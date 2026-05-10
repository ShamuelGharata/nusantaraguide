<?php

namespace App\Http\Controllers;

use App\Models\ItineraryPost;
use App\Models\Region;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItineraryController extends Controller
{
    /**
     * Public feed of published itineraries.
     * Supports filtering by region, tag, travel_style, duration.
     */
    public function index(Request $request)
    {
        $query = ItineraryPost::with(['user', 'region.province', 'tags'])
            ->where('status', 'published')
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at');

        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }
        if ($request->filled('style')) {
            $query->where('travel_style', $request->style);
        }
        if ($request->filled('max_days')) {
            $query->where('duration_days', '<=', $request->max_days);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('summary', 'like', '%'.$request->search.'%');
            });
        }

        $itineraries = $query->paginate(9)->withQueryString();
        $regions     = Region::with('province')->orderBy('name')->get();
        $tags        = Tag::all();

        return view('itineraries.index', compact('itineraries', 'regions', 'tags'));
    }

    /**
     * Show a single itinerary post with full day-by-day breakdown.
     */
    public function show($slug)
    {
        $itinerary = ItineraryPost::with([
            'user',
            'region.province',
            'tags',
            'days.stops.place',
            'comments.user',
        ])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        // Increment view count
        $itinerary->increment('views');

        $isLiked = Auth::check()
            ? $itinerary->likes()->where('user_id', Auth::id())->exists()
            : false;
        $isSaved = Auth::check()
            ? $itinerary->savedByUsers()->where('user_id', Auth::id())->exists()
            : false;

        // Related itineraries from same region
        $related = ItineraryPost::where('region_id', $itinerary->region_id)
            ->where('id', '!=', $itinerary->id)
            ->where('status', 'published')
            ->withCount('likes')
            ->latest()
            ->take(3)
            ->get();

        return view('itineraries.show', compact('itinerary', 'isLiked', 'isSaved', 'related'));
    }

    /**
     * Current user's own itinerary posts.
     */
    public function myPosts()
    {
        $itineraries = ItineraryPost::where('user_id', Auth::id())
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);

        return view('itineraries.my', compact('itineraries'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $regions = Region::with('province')->orderBy('name')->get();
        $tags    = Tag::all();
        return view('itineraries.create', compact('regions', 'tags'));
    }

    /**
     * Store a new itinerary post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'summary'           => 'required|string|max:500',
            'body'              => 'nullable|string',
            'region_id'         => 'nullable|exists:regions,id',
            'duration_days'     => 'required|integer|min:1|max:30',
            'estimated_budget'  => 'nullable|numeric|min:0',
            'travel_style'      => 'required|in:backpacker,budget,mid-range,luxury,family,solo,couple,group',
            'status'            => 'required|in:draft,published',
            'cover_image'       => 'nullable|image|max:2048',
            'tags'              => 'nullable|array',
            'tags.*'            => 'exists:tags,id',
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('itineraries', 'public');
        }

        $itinerary = ItineraryPost::create([
            'user_id'          => Auth::id(),
            'title'            => $validated['title'],
            'slug'             => $slug,
            'summary'          => $validated['summary'],
            'body'             => $validated['body'] ?? null,
            'region_id'        => $validated['region_id'] ?? null,
            'duration_days'    => $validated['duration_days'],
            'estimated_budget' => $validated['estimated_budget'] ?? null,
            'travel_style'     => $validated['travel_style'],
            'status'           => $validated['status'],
            'cover_image'      => $coverPath,
        ]);

        if (!empty($validated['tags'])) {
            $itinerary->tags()->sync($validated['tags']);
        }

        return redirect()->route('itinerary.show', $itinerary->slug)
            ->with('success', 'Itinerary berhasil dibuat!');
    }

    /**
     * Edit form (owner only).
     */
    public function edit($slug)
    {
        $itinerary = ItineraryPost::where('slug', $slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $regions = Region::with('province')->orderBy('name')->get();
        $tags    = Tag::all();

        return view('itineraries.edit', compact('itinerary', 'regions', 'tags'));
    }

    /**
     * Update itinerary (owner only).
     */
    public function update(Request $request, $slug)
    {
        $itinerary = ItineraryPost::where('slug', $slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'summary'          => 'required|string|max:500',
            'body'             => 'nullable|string',
            'region_id'        => 'nullable|exists:regions,id',
            'duration_days'    => 'required|integer|min:1|max:30',
            'estimated_budget' => 'nullable|numeric|min:0',
            'travel_style'     => 'required|in:backpacker,budget,mid-range,luxury,family,solo,couple,group',
            'status'           => 'required|in:draft,published',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
        ]);

        $itinerary->update($validated);

        if (isset($validated['tags'])) {
            $itinerary->tags()->sync($validated['tags']);
        }

        return redirect()->route('itinerary.show', $itinerary->slug)
            ->with('success', 'Itinerary berhasil diperbarui!');
    }

    /**
     * Delete itinerary (owner only).
     */
    public function destroy($slug)
    {
        $itinerary = ItineraryPost::where('slug', $slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $itinerary->delete();

        return redirect()->route('itinerary.my')
            ->with('success', 'Itinerary berhasil dihapus.');
    }

    /**
     * Toggle like on an itinerary.
     */
    public function toggleLike($id)
    {
        $itinerary = ItineraryPost::findOrFail($id);
        $user = Auth::user();

        $existing = $itinerary->likes()->where('user_id', $user->id)->first();
        if ($existing) {
            $itinerary->likes()->detach($user->id);
        } else {
            $itinerary->likes()->attach($user->id);
        }

        return back();
    }

    /**
     * Toggle save (bookmark) an itinerary.
     */
    public function toggleSave($id)
    {
        $itinerary = ItineraryPost::findOrFail($id);
        $itinerary->savedByUsers()->toggle(Auth::id());
        return back();
    }

    /**
     * Post a comment on an itinerary.
     */
    public function comment(Request $request, $id)
    {
        $request->validate(['comment' => 'required|string|max:1000']);

        $itinerary = ItineraryPost::findOrFail($id);
        $itinerary->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }
}
