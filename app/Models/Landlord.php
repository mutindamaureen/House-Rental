<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_id',
        'national_id',
        'company_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function houses()
    {
        return $this->hasMany(House::class, 'landlord_id');
    }
}
