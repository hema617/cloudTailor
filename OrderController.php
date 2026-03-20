<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Measurement;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{


    public function Orders(Request $request)
    {
        try {
            $search   = $request->search ?? null;
            $status   = $request->status ?? null;
            $perPage  = $request->per_page ?? 10;
            $slug   = $request->slug ?? null;
            $query = Order::with(['items'])->latest();

            if(getRoutePrefix() == 'tailor'){
                $query->where('tailor_id',authUserId());
            }
            if(getRoutePrefix() == 'customer'){
                $query->where('customer_id',authUserId());
            }


            // 🔍 Search (order id, customer name, etc.)
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('order_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // 🔄 Status filter
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            if($slug){

                if ($slug == 'new_order') {
    
                    // Example: orders created today OR pending
                    $query->whereDate('created_at', today())
                        ->where('status', 'pending');
                } elseif ($slug == 'on_going') {
    
                    // Ongoing orders (processing)
                    $query->where('status', 'processing');
                } elseif ($slug == 'completed') {
    
                    // Completed orders
                    $query->where('status', 'completed');
                }
            }

            // 📄 Pagination
            $orders = $query->paginate($perPage);
            return (new ResponseService())->success(
                'Order list fetched successfully',
                $orders
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while fetching orders',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'status'   => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return (new ResponseService())->error(
                'Validation error',
                422,
                ['errors' => $validator->errors()]
            );
        }

        try {
            $user = $request->user();

            // 🔍 Find order
            $order = Order::where('id', $request->order_id)->first();

            if (!$order) {
                return (new ResponseService())->error(
                    'Order not found',
                    404
                );
            }

            // 🔒 Optional: Restrict who can update
            // Example: Only owner or admin
            if ($user->role !== 'admin' && $order->customer_id !== $user->id) {
                return (new ResponseService())->error(
                    'Unauthorized to update this order',
                    403
                );
            }

            // 🔥 Optional: Prevent invalid transitions
            if ($order->status === 'completed') {
                return (new ResponseService())->error(
                    'Completed order cannot be updated',
                    400
                );
            }

            // ✅ Update status
            $order->update([
                'status' => $request->status
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Order status updated successfully',
                'data'    => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while updating order status',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /*
    =========================
    Place Order
    =========================
    */

    public function placeOrder(Request $request)
    {

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'tailor_id' => $request->tailor_id,
            'total' => $request->total,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        $cartItems = Cart::where('customer_id', $request->customer_id)->get();

        foreach ($cartItems as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'design_id' => $item->design_id,
                'quantity' => $item->quantity,
                'price' => $item->design->price
            ]);
        }

        Cart::where('customer_id', $request->customer_id)->delete();

        return response()->json([
            'message' => 'Order placed',
            'order' => $order
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
                ->where('tailor_id', $tailor_id)
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
            Order::with(['items', 'measurement'])
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
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => 'Order cancelled'
        ]);
    }


    /*
    =========================
    Update Order Status
    =========================
    */

    public function updateStatus(Request $request, $id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated'
        ]);
    }


    /*
    =========================
    Update Payment Status
    =========================
    */

    public function updatePayment(Request $request, $id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'payment_status' => $request->payment_status
        ]);

        return response()->json([
            'message' => 'Payment status updated'
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
            'order_id' => $request->order_id,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hip' => $request->hip,
            'length' => $request->length
        ]);

        return response()->json([
            'message' => 'Measurement added',
            'data' => $measurement
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
            Measurement::where('order_id', $order_id)->first()
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
            'message' => 'Order deleted'
        ]);
    }
}
