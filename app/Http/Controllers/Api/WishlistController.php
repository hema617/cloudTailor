<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{

    public function wishlist($customer_id)
    {

        return response()->json(
            Wishlist::with('design')
            ->where('customer_id',$customer_id)
            ->get()
        );

    }

    public function addWishlist(Request $request)
    {

        $wishlist = Wishlist::create([
            'customer_id'=>$request->customer_id,
            'design_id'=>$request->design_id
        ]);

        return response()->json([
            'message'=>'Added to wishlist',
            'data'=>$wishlist
        ]);

    }

    public function removeWishlist($id)
    {

        Wishlist::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Removed from wishlist'
        ]);

    }

}