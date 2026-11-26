<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'service',
        'request_body',
        'status_code',
        'response_body',
        'ip',
        'user_id',
    ];
}
