<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryStop extends Model
{
    protected $table = 'itinerary_stops';
    public $timestamps = false;

    protected $fillable = [
        'day_id', 'stop_type', 'place_id', 'flight_booking_id', 'hotel_booking_id',
        'custom_place_name', 'custom_title', 'custom_description',
        'visit_order', 'visit_time', 'duration_minutes', 'tip', 'cost',
    ];

    protected $casts = ['cost' => 'decimal:2'];

    public function day()           { return $this->belongsTo(ItineraryDay::class, 'day_id'); }
    public function place()         { return $this->belongsTo(Place::class); }
    public function flightBooking() { return $this->belongsTo(FlightBooking::class, 'flight_booking_id'); }
    public function hotelBooking()  { return $this->belongsTo(Booking::class, 'hotel_booking_id'); }

    public function getDisplayNameAttribute(): string
    {
        return match($this->stop_type) {
            'flight' => '✈️ ' . ($this->flightBooking ? $this->flightBooking->flight->origin->city . ' → ' . $this->flightBooking->flight->destination->city : 'Penerbangan'),
            'hotel'  => '🏨 ' . ($this->hotelBooking  ? $this->hotelBooking->place->name : 'Hotel'),
            'place'  => '📍 ' . ($this->place->name ?? $this->custom_place_name ?? 'Lokasi'),
            'custom' => '📝 ' . ($this->custom_title ?? 'Aktivitas'),
            default  => $this->custom_place_name ?? 'Aktivitas',
        };
    }

    public function getDisplayCostAttribute(): ?float
    {
        if ($this->cost) return (float) $this->cost;
        return match($this->stop_type) {
            'flight' => $this->flightBooking ? (float) $this->flightBooking->total_price : null,
            'hotel'  => $this->hotelBooking  ? (float) $this->hotelBooking->total_price  : null,
            default  => null,
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->stop_type) {
            'flight' => '✈️', 'hotel' => '🏨', 'place' => '📍', 'custom' => '📝', default => '📌',
        };
    }
}