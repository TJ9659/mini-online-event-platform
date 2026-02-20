<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'title',
        'slug',
        'description',
        'is_featured',
        'status',
        'platform_name',
        'meeting_link',
        'start_time',
        'end_time',
        'cover_image',
        'capacity',
        'speaker',
        'timezone',
    ];
    public const EVENT_STATUS = [
        'published',
        'draft',
        'cancelled',
    ];
    protected $hidden = [
        'meeting_link',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    protected $appends = ['formatted_date', 'formatted_start_time', 'formatted_end_time'];

    public function getFormattedDateAttribute()
    {
        return $this->start_time?->format('M d');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time?->format('g:i A');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time?->format('g:i A');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // public function scopeUpcoming($query)
    // {
    //     return $query->where('start_time', '>=', now());
    // }

    // public function scopePast($query)
    // {
    //     return $query->where('end_time', '<', now());
    // }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }
    

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'tickets', 'event_id', 'user_id')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function isFull(): bool
    {
        return $this->tickets()->where('status', 'confirmed')->count() >= $this->capacity;
    }

    public function isPast(): bool
    {
        return $this->start_time->isPast();
    }
}
