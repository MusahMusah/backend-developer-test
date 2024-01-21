<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'required_achievements'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserBadge::class);
    }
}
