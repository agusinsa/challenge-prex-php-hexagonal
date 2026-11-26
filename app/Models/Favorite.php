<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'gif_id',
        'alias',
        'user_id',
    ];
}
