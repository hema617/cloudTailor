<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{

    public function payment(Request $request)
    {

        $payment = Payment::create([
            'order_id'=>$request->order_id,
            'amount'=>$request->amount,
            'method'=>$request->method,
            'status'=>'success'
        ]);

        return response()->json([
            'message'=>'Payment successful',
            'data'=>$payment
        ]);

    }

    public function paymentDetails($order_id)
    {

        return response()->json(
            Payment::where('order_id',$order_id)->first()
        );

    }

    public function refund($id)
    {

        $payment = Payment::findOrFail($id);

        $payment->update([
            'status'=>'refunded'
        ]);

        return response()->json([
            'message'=>'Payment refunded'
        ]);

    }

}