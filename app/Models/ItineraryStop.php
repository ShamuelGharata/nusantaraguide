<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryStop extends Model
{
    protected $table = 'itinerary_stops';

    public $timestamps = false;

    protected $fillable = [
        'day_id', 'place_id', 'custom_place_name',
        'visit_order', 'visit_time', 'duration_minutes', 'tip', 'cost',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────

    // Each stop belongs to one day
    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'day_id');
    }

    // A stop may link to a place in the database (or be a custom location)
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    // ── ACCESSORS ─────────────────────────────────────────

    // Returns the display name whether it's a linked place or a custom one
    // Usage in blade: {{ $stop->display_name }}
    public function getDisplayNameAttribute(): string
    {
        return $this->place?->name ?? $this->custom_place_name ?? 'Lokasi tidak diketahui';
    }
}
