<?php

namespace App\Models;

use App\Concerns\Enums\AchievementTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => AchievementTypeEnum::class,
        'created_at' => 'datetime'
    ];

    protected $fillable = [
        'count',
        'text',
        'type',
    ];
}
