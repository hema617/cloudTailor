<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Design;

class CartController extends Controller
{

    /*
    =========================
    Get Cart Items
    =========================
    */

    public function cart($customer_id)
    {

        $cart = Cart::with('design')
            ->where('customer_id',$customer_id)
            ->get();

        return response()->json($cart);
    }

    /*
    =========================
    Add To Cart
    =========================
    */

    public function addToCart(Request $request)
    {

        $design = Design::findOrFail($request->design_id);

        $cart = Cart::where('customer_id',$request->customer_id)
                    ->where('design_id',$request->design_id)
                    ->first();

        if($cart)
        {

            $cart->quantity += $request->quantity;
            $cart->save();

        }else{

            $cart = Cart::create([
                'customer_id'=>$request->customer_id,
                'design_id'=>$request->design_id,
                'quantity'=>$request->quantity
            ]);

        }

        return response()->json([
            'message'=>'Added to cart',
            'data'=>$cart
        ]);
    }


    /*
    =========================
    Update Cart Item
    =========================
    */

    public function updateCart(Request $request,$id)
    {

        $cart = Cart::findOrFail($id);

        $cart->update([
            'quantity'=>$request->quantity
        ]);

        return response()->json([
            'message'=>'Cart updated'
        ]);
    }


    /*
    =========================
    Remove Item From Cart
    =========================
    */

    public function removeFromCart($id)
    {

        Cart::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Item removed'
        ]);
    }


    /*
    =========================
    Clear Cart
    =========================
    */

    public function clearCart($customer_id)
    {

        Cart::where('customer_id',$customer_id)->delete();

        return response()->json([
            'message'=>'Cart cleared'
        ]);
    }


    /*
    =========================
    Cart Count
    =========================
    */

    public function cartCount($customer_id)
    {

        $count = Cart::where('customer_id',$customer_id)->count();

        return response()->json([
            'count'=>$count
        ]);
    }


    /*
    =========================
    Cart Total
    =========================
    */

    public function cartTotal($customer_id)
    {

        $items = Cart::with('design')
            ->where('customer_id',$customer_id)
            ->get();

        $total = 0;

        foreach($items as $item)
        {

            $total += $item->design->price * $item->quantity;

        }

        return response()->json([
            'total'=>$total
        ]);
    }

}