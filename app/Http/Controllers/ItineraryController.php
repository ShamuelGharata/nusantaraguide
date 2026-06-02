<?php

namespace App\Http\Controllers;

use App\Models\ItineraryPost;
use App\Models\ItineraryDay;
use App\Models\ItineraryStop;
use App\Models\ItineraryCopy;
use App\Models\Region;
use App\Models\Tag;
use App\Models\Place;
use App\Models\FlightBooking;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItineraryController extends Controller
{
    // ── PUBLIC FEED ───────────────────────────────────────────

    public function index(Request $request)
    {
        $query = ItineraryPost::with(['user', 'region.province', 'tags'])
            ->where('status', 'published')
            ->withCount(['likes', 'comments', 'copies'])
            ->orderByDesc('created_at');

        if ($request->filled('region'))  $query->where('region_id', $request->region);
        if ($request->filled('tag'))     $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        if ($request->filled('style'))   $query->where('travel_style', $request->style);
        if ($request->filled('max_days')) $query->where('duration_days', '<=', $request->max_days);
        if ($request->filled('verified')) $query->where('is_verified', 1);
        if ($request->filled('search'))  $query->where(function($q) use ($request) {
            $q->where('title', 'like', '%'.$request->search.'%')
              ->orWhere('summary', 'like', '%'.$request->search.'%');
        });

        $itineraries = $query->paginate(9)->withQueryString();
        $regions     = Region::with('province')->orderBy('name')->get();
        $tags        = Tag::all();

        return view('itineraries.index', compact('itineraries', 'regions', 'tags'));
    }

    // ── SHOW SINGLE POST ──────────────────────────────────────

    public function show($slug)
    {
        $itinerary = ItineraryPost::with([
            'user',
            'region.province',
            'tags',
            'days.stops.place',
            'days.stops.flightBooking.flight.airline',
            'days.stops.flightBooking.flight.origin',
            'days.stops.flightBooking.flight.destination',
            'days.stops.hotelBooking.place',
            'days.stops.hotelBooking.room',
            'comments.user',
        ])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        $itinerary->increment('views');

        $isLiked  = Auth::check() ? $itinerary->likes()->where('user_id', Auth::id())->exists() : false;
        $isSaved  = Auth::check() ? $itinerary->savedByUsers()->where('user_id', Auth::id())->exists() : false;
        $isCopied = Auth::check() ? $itinerary->copies()->where('user_id', Auth::id())->exists() : false;

        // Calculate total cost from all stops
        $totalCost = $itinerary->days->flatMap(fn($d) => $d->stops)->sum(fn($s) => $s->display_cost ?? 0);

        $related = ItineraryPost::where('region_id', $itinerary->region_id)
            ->where('id', '!=', $itinerary->id)
            ->where('status', 'published')
            ->withCount('likes')
            ->latest()->take(3)->get();

        return view('itineraries.show', compact(
            'itinerary', 'isLiked', 'isSaved', 'isCopied', 'totalCost', 'related'
        ));
    }

    // ── CREATE FORM ───────────────────────────────────────────

    public function create()
    {
        $regions = Region::with('province')->orderBy('name')->get();
        $tags    = Tag::all();

        // Load user's existing bookings to link
        $flightBookings = FlightBooking::with(['flight.airline', 'flight.origin', 'flight.destination'])
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->latest()->get();

        $hotelBookings = Booking::with(['place', 'room'])
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->latest()->get();

        $places = Place::with(['region', 'category'])->orderBy('name')->get();

        // Pre-format data as simple arrays for JavaScript
        $flightBookingsJson = $flightBookings->map(fn($b) => [
            'id'    => $b->id,
            'label' => $b->flight->airline->name . ' ' . $b->flight->flight_number . ': '
                     . $b->flight->origin->city . ' → ' . $b->flight->destination->city
                     . ' (' . $b->travel_date->format('d M Y') . ')',
            'price' => (float) $b->total_price,
        ])->values()->toArray();

        $hotelBookingsJson = $hotelBookings->map(fn($b) => [
            'id'    => $b->id,
            'label' => $b->place->name . ' - ' . $b->room->name
                     . ' (' . $b->check_in->format('d M') . ' – ' . $b->check_out->format('d M Y') . ')',
            'price' => (float) $b->total_price,
        ])->values()->toArray();

        $placesJson = $places->map(fn($p) => [
            'id'    => $p->id,
            'label' => $p->name . ($p->region ? ' (' . $p->region->name . ')' : ''),
        ])->values()->toArray();

        return view('itineraries.create', compact(
            'regions', 'tags', 'flightBookings', 'hotelBookings', 'places',
            'flightBookingsJson', 'hotelBookingsJson', 'placesJson'
        ));
    }

    // ── STORE NEW ITINERARY ───────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'summary'          => 'required|string|max:500',
            'region_id'        => 'nullable|exists:regions,id',
            'duration_days'    => 'required|integer|min:1|max:30',
            'estimated_budget' => 'nullable|numeric|min:0',
            'actual_budget'    => 'nullable|numeric|min:0',
            'travel_style'     => 'required|in:backpacker,budget,mid-range,luxury,family,solo,couple,group',
            'status'           => 'required|in:draft,published',
            'trip_rating'      => 'nullable|integer|min:1|max:5',
            'trip_tips'        => 'nullable|string|max:1000',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
            'days'             => 'required|array',
            'days.*.title'     => 'nullable|string|max:255',
            'days.*.notes'     => 'nullable|string',
            'days.*.stops'     => 'nullable|array',
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('itineraries', 'public');
        }

        DB::transaction(function() use ($validated, $slug, $coverPath, $request) {
            $itinerary = ItineraryPost::create([
                'user_id'          => Auth::id(),
                'title'            => $validated['title'],
                'slug'             => $slug,
                'summary'          => $validated['summary'],
                'body'             => $request->input('body'),
                'region_id'        => $validated['region_id'] ?? null,
                'duration_days'    => $validated['duration_days'],
                'estimated_budget' => $validated['estimated_budget'] ?? null,
                'actual_budget'    => $validated['actual_budget'] ?? null,
                'travel_style'     => $validated['travel_style'],
                'status'           => $validated['status'],
                'cover_image'      => $coverPath,
                'trip_rating'      => $validated['trip_rating'] ?? null,
                'trip_tips'        => $validated['trip_tips'] ?? null,
            ]);

            if (!empty($validated['tags'])) {
                $itinerary->tags()->sync($validated['tags']);
            }

            // Save days and stops
            foreach ($validated['days'] as $dayIndex => $dayData) {
                $day = ItineraryDay::create([
                    'itinerary_id' => $itinerary->id,
                    'day_number'   => $dayIndex + 1,
                    'title'        => $dayData['title'] ?? null,
                    'notes'        => $dayData['notes'] ?? null,
                ]);

                if (!empty($dayData['stops'])) {
                    foreach ($dayData['stops'] as $stopIndex => $stopData) {
                        ItineraryStop::create([
                            'day_id'             => $day->id,
                            'stop_type'          => $stopData['type'] ?? 'custom',
                            'place_id'           => $stopData['place_id'] ?? null,
                            'flight_booking_id'  => $stopData['flight_booking_id'] ?? null,
                            'hotel_booking_id'   => $stopData['hotel_booking_id'] ?? null,
                            'custom_place_name'  => $stopData['custom_place_name'] ?? null,
                            'custom_title'       => $stopData['custom_title'] ?? null,
                            'custom_description' => $stopData['custom_description'] ?? null,
                            'visit_order'        => $stopIndex + 1,
                            'visit_time'         => $stopData['visit_time'] ?? null,
                            'duration_minutes'   => $stopData['duration_minutes'] ?? null,
                            'tip'                => $stopData['tip'] ?? null,
                            'cost'               => $stopData['cost'] ?? null,
                        ]);
                    }
                }
            }

            // Auto-verify if any real bookings are linked
            $itinerary->checkAndMarkVerified();
        });

        return redirect()->route('itinerary.index')
            ->with('success', 'Itinerary berhasil dibuat!');
    }

    // ── MY ITINERARIES ────────────────────────────────────────

    public function myPosts()
    {
        $itineraries = ItineraryPost::where('user_id', Auth::id())
            ->withCount(['likes', 'comments', 'copies'])
            ->latest()->paginate(10);

        $copiedItineraries = ItineraryCopy::with(['originalItinerary.user', 'originalItinerary.region'])
            ->where('user_id', Auth::id())
            ->latest('copied_at')->get();

        return view('itineraries.my', compact('itineraries', 'copiedItineraries'));
    }

    // ── SOCIAL ACTIONS ────────────────────────────────────────

    public function toggleLike($id)
    {
        $itinerary = ItineraryPost::findOrFail($id);
        $itinerary->likes()->toggle(Auth::id());
        return back();
    }

    public function toggleSave($id)
    {
        $itinerary = ItineraryPost::findOrFail($id);
        $itinerary->savedByUsers()->toggle(Auth::id());
        return back();
    }

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

    // ── COPY ITINERARY ────────────────────────────────────────

    public function copy($id)
    {
        $itinerary = ItineraryPost::where('status', 'published')->findOrFail($id);

        // Prevent copying own itinerary
        if ($itinerary->user_id === Auth::id()) {
            return back()->with('error', 'Kamu tidak bisa menyalin itinerary sendiri.');
        }

        ItineraryCopy::firstOrCreate([
            'original_itinerary_id' => $itinerary->id,
            'user_id'               => Auth::id(),
        ], [
            'copied_at' => now(),
        ]);

        return redirect()->route('itinerary.my')
            ->with('success', 'Itinerary "' . $itinerary->title . '" berhasil disalin ke koleksimu!');
    }

    // ── DELETE ────────────────────────────────────────────────

    public function destroy($slug)
    {
        $itinerary = ItineraryPost::where('slug', $slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $itinerary->delete();

        return redirect()->route('itinerary.my')
            ->with('success', 'Itinerary berhasil dihapus.');
    }
}