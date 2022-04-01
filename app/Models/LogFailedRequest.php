<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogFailedRequest extends Model
{
    protected $fillable = [
        'request_data',
        'status_code',
        'messages',
        'detail'
    ];
}
