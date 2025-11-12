<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $landlord_id
 * @property-read Landlord $landlord
 */
class House extends Model
{


    public function landlord()
    {
        return $this->belongsTo(Landlord::class, 'landlord_id');
    }

/**
 * Chat messages for this house
 */
public function chatMasseges()
{
    return $this->hasMany(Chatmasseges::class);
}

}
