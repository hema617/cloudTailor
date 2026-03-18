<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tailor;
use App\Models\Order;
use App\Services\ResponseService;

class AdminController extends Controller
{

    /*
    =========================
    Dashboard
    =========================
    */

    public function dashboard()
    {

        return response()->json([
            'User' => User::count(),
            'tailors' => Tailor::count(),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count()
        ]);
    }


    /*
    =========================
    User
    =========================
    */

    public function customers(Request $request)
    {
        try {
            $role    = $request->slug ?? 'customer';
            $perPage = $request->per_page ?? 10;

            // Build query
            $query = User::where('role', $role);

            // Apply status filter only if provided
            if (!is_null($request->status)) {
                $query->where('status', $request->status);
            }

            // Paginate
            $users = $query->paginate($perPage);

            return (new ResponseService())->success(
                'Customer List Get Successfully',
                $users
            );
        } catch (\Throwable $th) {
            return (new ResponseService())->error($th->getMessage(), 422);
        }
    }
    public function changeUserStatus(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'status' => 'required|in:0,1,2' // adjust based on your system (active/inactive)
            ]);

            $id = $request->route('id');

            // Find user
            $user = User::find($id);

            if (!$user) {
                return (new ResponseService())->error('User not found', 422);
            }
            if ($user == 'admin') {
                return (new ResponseService())->error('User not found', 422);
            }
            // Update status
            $user->update([
                'status' => $request->status
            ]);
            // 🔥 Revoke all tokens if user is inactive
            if ($request->status != 1) {
                $user->tokens()->delete(); // Sanctum tokens
            }
            return (new ResponseService())->success(
                'Customer Status Updated Successfully',
                $user
            );
        } catch (\Throwable $th) {
            return (new ResponseService())->error($th->getMessage(), 422);
        }
    }

    public function detail(Request $request)
    {
        try {
            $user = User::find($request->route('id'));

            if (!$user) {
                return (new ResponseService())->error('User not found', 422);
            }

            return (new ResponseService())->success(
                'User Detail Get Successfully',
                $user
            );
        } catch (\Throwable $th) {
            return (new ResponseService())->error($th->getMessage(), 422);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $id = $request->route('id');
            $user = User::find($id);

            if (!$user) {
                return (new ResponseService())->error('User not found', 422);
            }

            // 🔒 Optional: Prevent self delete
            if (auth()->id() == $user->id) {
                return (new ResponseService())->error('You cannot delete your own account', 422);
            }

            // 🔥 Revoke all tokens (Sanctum)
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // 🗑️ Delete user
            $user->delete();

            return (new ResponseService())->success('User deleted successfully');
        } catch (\Throwable $th) {
            return (new ResponseService())->error($th->getMessage(), 422);
        }
    }







    /*
    =========================
    Orders
    =========================
    */

    public function orders()
    {

        return response()->json(
            Order::with(['items'])->latest()->get()
        );
    }


    public function updateOrderStatus(Request $request, $id)
    {

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated'
        ]);
    }
}
