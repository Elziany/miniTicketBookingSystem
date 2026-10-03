<?php

namespace App\Models;

use App\Traits\HasSeatGrid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    use HasFactory , HasSeatGrid;

    protected $fillable = [
        'name',
        'address',
        'layout_type',
        'row_count',
        'column_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'row_count'    => 'integer',
            'column_count' => 'integer',
        ];
    }

    /**
     * Calculate total maximum capacity using Attribute class.
     */
    protected function totalCapacity(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->row_count ?? 0) * ($this->column_count ?? 0),
        );
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hall_agent');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
