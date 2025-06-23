<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable= [
        'action',
        'model_type',
        'model_id',
        'payload',
        'user_id',
        'ip_address',
        'active',
        'date_log',
        'computer_name',
    ];
}
