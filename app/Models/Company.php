<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
      use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'email',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function recruiters()
    {
        return $this->users()->whereHas('role', function ($query) {
            $query->where('name', 'recruiter');
        });
    }
}
