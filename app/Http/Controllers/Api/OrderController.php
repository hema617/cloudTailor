<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Measurement;

class OrderController extends Controller
{

    /*
    =========================
    Place Order
    =========================
    */

    public function placeOrder(Request $request)
    {

        $order = Order::create([
            'customer_id'=>$request->customer_id,
            'tailor_id'=>$request->tailor_id,
            'total'=>$request->total,
            'status'=>'pending',
            'payment_status'=>'unpaid'
        ]);

        $cartItems = Cart::where('customer_id',$request->customer_id)->get();

        foreach($cartItems as $item)
        {

            OrderItem::create([
                'order_id'=>$order->id,
                'design_id'=>$item->design_id,
                'quantity'=>$item->quantity,
                'price'=>$item->design->price
            ]);

        }

        Cart::where('customer_id',$request->customer_id)->delete();

        return response()->json([
            'message'=>'Order placed',
            'order'=>$order
        ]);
    }


    /*
    =========================
    Customer Orders
    =========================
    */

    public function customerOrders($customer_id)
    {

        return response()->json(
            Order::with('items')
            ->where('customer_id',$customer_id)
            ->latest()
            ->get()
        );

    }


    /*
    =========================
    Tailor Orders
    =========================
    */

    public function tailorOrders($tailor_id)
    {

        return response()->json(
            Order::with('items')
            ->where('tailor_id',$tailor_id)
            ->latest()
            ->get()
        );

    }


    /*
    =========================
    Order Details
    =========================
    */

    public function orderDetails($id)
    {

        return response()->json(
            Order::with(['items','measurement'])
            ->findOrFail($id)
        );

    }


    /*
    =========================
    Cancel Order
    =========================
    */

    public function cancelOrder($id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'status'=>'cancelled'
        ]);

        return response()->json([
            'message'=>'Order cancelled'
        ]);
    }


    /*
    =========================
    Update Order Status
    =========================
    */

    public function updateStatus(Request $request,$id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'status'=>$request->status
        ]);

        return response()->json([
            'message'=>'Order status updated'
        ]);

    }


    /*
    =========================
    Update Payment Status
    =========================
    */

    public function updatePayment(Request $request,$id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'payment_status'=>$request->payment_status
        ]);

        return response()->json([
            'message'=>'Payment status updated'
        ]);

    }


    /*
    =========================
    Add Measurement
    =========================
    */

    public function addMeasurement(Request $request)
    {

        $measurement = Measurement::create([
            'order_id'=>$request->order_id,
            'chest'=>$request->chest,
            'waist'=>$request->waist,
            'hip'=>$request->hip,
            'length'=>$request->length
        ]);

        return response()->json([
            'message'=>'Measurement added',
            'data'=>$measurement
        ]);

    }


    /*
    =========================
    Get Measurement
    =========================
    */

    public function measurement($order_id)
    {

        return response()->json(
            Measurement::where('order_id',$order_id)->first()
        );

    }


    /*
    =========================
    Delete Order
    =========================
    */

    public function deleteOrder($id)
    {

        Order::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Order deleted'
        ]);

    }

}