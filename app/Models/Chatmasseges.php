<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatmasseges extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the house associated with this message
     */
    public function house()
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Scope to get unread messages for a user
     */
    public function scopeUnreadFor($query, $userId)
    {
        return $query->where('receiver_id', $userId)
                     ->where('is_read', false);
    }

    /**
     * Scope to get conversation between two users about a house
     */
    public function scopeConversation($query, $user1Id, $user2Id, $houseId)
    {
        return $query->where('house_id', $houseId)
                     ->where(function ($q) use ($user1Id, $user2Id) {
                         $q->where(function ($q2) use ($user1Id, $user2Id) {
                             $q2->where('sender_id', $user1Id)
                                ->where('receiver_id', $user2Id);
                         })->orWhere(function ($q2) use ($user1Id, $user2Id) {
                             $q2->where('sender_id', $user2Id)
                                ->where('receiver_id', $user1Id);
                         });
                     })
                     ->orderBy('created_at', 'asc');
    }
}
