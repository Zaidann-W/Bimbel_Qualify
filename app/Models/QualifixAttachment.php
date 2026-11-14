<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualifixAttachment extends Model
{
    use HasFactory;

    protected $table = 'qualifix_attachments';

    protected $fillable = [
        'ticket_id',
        'answer_id',
        'file_id',
    ];

    public function ticket()
    {
        return $this->belongsTo(QualifixTicket::class, 'ticket_id');
    }

    public function answer()
    {
        return $this->belongsTo(QualifixAnswer::class, 'answer_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
