<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\Wishlist;
use App\Models\Notification;
use App\Models\City;
use App\Models\DeliveryCharge;

class UserController extends Controller
{

    /*
    =========================
    Address APIs
    =========================
    */

    public function addresses(Request $request)
    {
        $addresses = UserAddress::where('user_id',$request->user()->id)->get();

        return response()->json($addresses);
    }


    public function addAddress(Request $request)
    {

        $address = UserAddress::create([
            'user_id'=>$request->user()->id,
            'city_id'=>$request->city_id,
            'address'=>$request->address,
            'pincode'=>$request->pincode
        ]);

        return response()->json([
            'message'=>'Address added',
            'data'=>$address
        ]);
    }


    public function updateAddress(Request $request,$id)
    {

        $address = UserAddress::findOrFail($id);

        $address->update([
            'city_id'=>$request->city_id,
            'address'=>$request->address,
            'pincode'=>$request->pincode
        ]);

        return response()->json([
            'message'=>'Address updated'
        ]);
    }


    public function deleteAddress($id)
    {

        UserAddress::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Address deleted'
        ]);
    }


    public function getAddress($id)
    {

        $address = UserAddress::findOrFail($id);

        return response()->json($address);
    }


    /*
    =========================
    Wishlist APIs
    =========================
    */

    public function wishlist(Request $request)
    {

        $wishlist = Wishlist::with('design')
            ->where('user_id',$request->user()->id)
            ->get();

        return response()->json($wishlist);
    }


    public function addWishlist(Request $request)
    {

        $item = Wishlist::create([
            'user_id'=>$request->user()->id,
            'design_id'=>$request->design_id
        ]);

        return response()->json([
            'message'=>'Added to wishlist',
            'data'=>$item
        ]);
    }


    public function removeWishlist($design_id,Request $request)
    {

        Wishlist::where('user_id',$request->user()->id)
        ->where('design_id',$design_id)
        ->delete();

        return response()->json([
            'message'=>'Removed from wishlist'
        ]);
    }


    /*
    =========================
    Notification APIs
    =========================
    */

    public function notifications(Request $request)
    {

        $notifications = Notification::where('user_id',$request->user()->id)
        ->latest()
        ->get();

        return response()->json($notifications);
    }


    public function readNotification($id)
    {

        $notification = Notification::findOrFail($id);

        $notification->update([
            'is_read'=>1
        ]);

        return response()->json([
            'message'=>'Notification marked as read'
        ]);
    }


    public function deleteNotification($id)
    {

        Notification::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Notification deleted'
        ]);
    }


    /*
    =========================
    Location APIs
    =========================
    */

    public function cities()
    {

        return response()->json(
            City::where('status',1)->get()
        );
    }


    public function city($id)
    {

        return response()->json(
            City::findOrFail($id)
        );
    }


    public function deliveryCharge($city_id)
    {

        $charge = DeliveryCharge::where('city_id',$city_id)->first();

        return response()->json($charge);
    }

}