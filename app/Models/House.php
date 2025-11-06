<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    public function landlord()
    {
        return $this->belongsTo(Landlord::class, 'landlord_id');
    }

}
