<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Task extends Model
{
    

    protected $fillable = [
        'title',
        'description',
        'status',
        'date_of_end',
    ];

    protected $attributes = [
        'status' => 0
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
