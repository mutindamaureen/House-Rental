<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chatmasseges;
use App\Models\House;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    /**
     * Display chat list (conversations)
     */
    public function index()
    {
        $userId = Auth::id();

        // Get all unique conversations for the current user
        $conversations = Chatmasseges::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'house'])
            ->get()
            ->groupBy(function($message) use ($userId) {
                // Group by the other person in the conversation and house
                $otherUserId = $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
                return $otherUserId . '-' . $message->house_id;
            })
            ->map(function($messages) use ($userId) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $otherUserId = $lastMessage->sender_id == $userId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $otherUser = User::find($otherUserId);

                // Count unread messages from this user
                $unreadCount = $messages->where('receiver_id', $userId)
                                       ->where('is_read', false)
                                       ->count();

                return [
                    'other_user' => $otherUser,
                    'house' => $lastMessage->house,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc('last_message.created_at')
            ->values();

        return view('home.chat.index', compact('conversations'));
    }

    /**
     * Show chat window for specific house and landlord
     */
    public function show($houseId, $landlordId)
    {
        $house = House::findOrFail($houseId);
        $landlord = User::findOrFail($landlordId);
        $currentUser = Auth::user();

        // Verify landlord owns this house
        if ($house->landlord_id != $landlord->id) {
            toastr()->closeButton()->error('Invalid landlord for this property.');
            return redirect()->back();
        }

        // Get conversation messages
        $messages = Chatmasseges::conversation($currentUser->id, $landlordId, $houseId)->get();

        // Mark all received messages as read
        Chatmasseges::where('house_id', $houseId)
                   ->where('sender_id', $landlordId)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', false)
                   ->update(['is_read' => true, 'read_at' => now()]);

        // Generate WhatsApp link
        $whatsappUrl = $this->generateWhatsAppUrl($landlord, $house);

        return view('home.chat.show', compact('house', 'landlord', 'messages', 'whatsappUrl'));
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'house_id' => 'required|exists:houses,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $message = Chatmasseges::create([
                'house_id' => $request->house_id,
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Send email notification to receiver
            $receiver = User::find($request->receiver_id);
            $sender = Auth::user();
            $house = House::find($request->house_id);

            if ($receiver->email) {
                Mail::raw(
                    "Hello {$receiver->name},\n\n" .
                    "You have a new message from {$sender->name} regarding {$house->title}.\n\n" .
                    "Message: {$request->message}\n\n" .
                    "Log in to reply: " . url('/chat'),
                    function ($mail) use ($receiver) {
                        $mail->to($receiver->email)
                             ->subject('New Message - Property Inquiry');
                    }
                );
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load(['sender', 'receiver']),
                ]);
            }

            toastr()->closeButton()->success('Message sent successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            toastr()->closeButton()->error('Error sending message: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get messages for a conversation (AJAX)
     */
    public function getMessages($houseId, $otherUserId)
    {
        try {
            $currentUserId = Auth::id();

            $messages = Chatmasseges::conversation($currentUserId, $otherUserId, $houseId)
                                  ->with(['sender', 'receiver'])
                                  ->get();

            // Mark messages as read
            Chatmasseges::where('house_id', $houseId)
                       ->where('sender_id', $otherUserId)
                       ->where('receiver_id', $currentUserId)
                       ->where('is_read', false)
                       ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $count = Chatmasseges::unreadFor(Auth::id())->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead($houseId, $otherUserId)
    {
        try {
            Chatmasseges::where('house_id', $houseId)
                       ->where('sender_id', $otherUserId)
                       ->where('receiver_id', Auth::id())
                       ->where('is_read', false)
                       ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate WhatsApp URL
     */
    private function generateWhatsAppUrl($landlord, $house)
    {
        if (!$landlord->phone) {
            return null;
        }

        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $landlord->phone);

        // Convert to international format (254 for Kenya)
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }

        $currentUser = Auth::user();
        $message = urlencode(
            "🏠 *Property Inquiry*\n\n" .
            "Hello {$landlord->name},\n\n" .
            "I'm interested in: *{$house->title}*\n" .
            "Location: {$house->location}\n" .
            "Price: KES " . number_format($house->price) . "\n\n" .
            "From: {$currentUser->name}\n" .
            "Phone: {$currentUser->phone}\n" .
            "Email: {$currentUser->email}"
        );

        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Admin view all chats
     */
    public function adminIndex()
    {
        $conversations = Chatmasseges::with(['sender', 'receiver', 'house'])
            ->get()
            ->groupBy(function($message) {
                return $message->sender_id . '-' . $message->receiver_id . '-' . $message->house_id;
            })
            ->map(function($messages) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                return [
                    'sender' => $lastMessage->sender,
                    'receiver' => $lastMessage->receiver,
                    'house' => $lastMessage->house,
                    'last_message' => $lastMessage,
                    'message_count' => $messages->count(),
                ];
            })
            ->sortByDesc('last_message.created_at')
            ->values();

        return view('admin.chat.index', compact('conversations'));
    }

    /**
     * Admin view specific conversation
     */
    public function adminShow($senderId, $receiverId, $houseId)
    {
        $sender = User::findOrFail($senderId);
        $receiver = User::findOrFail($receiverId);
        $house = House::findOrFail($houseId);

        $messages = Chatmasseges::conversation($senderId, $receiverId, $houseId)->get();

        return view('admin.chat.show', compact('sender', 'receiver', 'house', 'messages'));
    }

    /**
     * Delete a message (Admin only)
     */
    public function deleteMessage($id)
    {
        try {
            $message = Chatmasseges::findOrFail($id);
            $message->delete();

            toastr()->closeButton()->success('Message deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting message: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
