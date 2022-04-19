<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
class Task extends Model
{
    // renommer les champs
    const CREATED_AT = 'creation_task_date';
    const UPDATED_AT = 'last_update_task_date';

    protected $fillable = [
        'title',
        'description',
        'status',
        'date_of_end',
        'user_id'
    ];

    protected $attributes = [
        'status' => 0
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
