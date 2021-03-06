<?php

namespace DigitalEquation\TeamworkDesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupportTicket extends Model
{
    use Notifiable;

    protected $fillable = ['user_id', 'ticket_id', 'event_creator_id'];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(config('teamwork-desk.user_model'));
    }
}
