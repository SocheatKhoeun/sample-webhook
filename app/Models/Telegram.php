<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Telegram extends Model
{
    use HasFactory;

    protected $table = 'telegrams';
    protected $primaryKey = 'id'; // Corrected key

    protected $fillable = [
        'user_id',
        'app_key',
        'chatBotID',
        'tel_username',
        'url_redirect', // Added for storing webhook URL
    ];
}
