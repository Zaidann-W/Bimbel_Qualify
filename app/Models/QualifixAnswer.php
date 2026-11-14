<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualifixAnswer extends Model
{
    use HasFactory;

    protected $table = 'qualifix_answers';

    protected $fillable = [
        'ticket_id',
        'staff_id',
        'body',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(QualifixTicket::class, 'ticket_id');
    }

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function attachments()
    {
        return $this->hasMany(QualifixAttachment::class, 'answer_id');
    }
}
