<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\House;
use App\Models\Category;
use App\Models\Landlord;
use App\Models\User;
/**
 * @property int $landlord_id
 * @property-read Landlord $landlord
 */
class HomeController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function home(){
        return view('home.index');
    }

    public function login_home(){
        // Only show available houses
        $house = House::where('status', 'available')
            ->latest()
            ->paginate(10);
        return view('home.index', compact('house'));
    }

    public function house_details($id)
    {
        $house = House::with('landlord.user')->findOrFail($id);

        // Optional: Redirect if house is not available
        if ($house->status !== 'available') {
            toastr()->closeButton()->warning('This house is no longer available.');
            return redirect('/see_house');
        }

        // Get landlord details
        $landlord = null;
        if ($house->landlord_id) {
            $landlordRecord = Landlord::with('user')->find($house->landlord_id);
            if ($landlordRecord) {
                $landlord = $landlordRecord->user;
            }
        }

        // Generate WhatsApp URL
        $whatsappUrl = null;
        if ($landlord && $landlord->phone) {
            $whatsappUrl = $this->generateWhatsAppUrl($landlord, $house);
        }

        return view('home.house_details', compact('house', 'landlord', 'whatsappUrl'));
    }

    public function see_house(Request $request){
        $query = House::where('status', 'available');

        // Search by title or location
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('location', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $house = $query->paginate(10);
        $categories = Category::all(); // For filter dropdown

        return view('home.see_house', compact('house', 'categories'));
    }

    /**
     * Generate WhatsApp contact URL
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

        $currentUser = auth()->user();
        $userName = $currentUser ? $currentUser->name : 'A potential tenant';
        $userPhone = $currentUser ? $currentUser->phone : 'N/A';
        $userEmail = $currentUser ? $currentUser->email : 'N/A';

        $message = urlencode(
            "🏠 *Property Inquiry*\n\n" .
            "Hello {$landlord->name},\n\n" .
            "I'm interested in: *{$house->title}*\n" .
            "Location: {$house->location}\n" .
            "Price: KES " . number_format($house->price) . "\n\n" .
            "From: {$userName}\n" .
            "Phone: {$userPhone}\n" .
            "Email: {$userEmail}"
        );

        return "https://wa.me/{$phone}?text={$message}";
    }
}
