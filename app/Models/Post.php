<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Post extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'news';
    protected $primaryKey = 'incremental_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'link',
        'posted_at',
        'points'
    ];

    protected $casts = [
      'points' => 'integer'
    ];

    /**
     * @var array<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
