<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tailor;
use App\Models\TailorService;
use App\Models\TailorPortfolio;
use App\Models\TailorAvailability;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Validator;

class TailorController extends Controller
{

    /*
    =========================
    Tailor APIs
    =========================
    */

    public function tailors()
    {
        return response()->json(
            Tailor::with('location')->get()
        );
    }

    public function tailor($id)
    {
        $tailor = Tailor::with([
            'location',
            'services',
            'portfolios'
        ])->findOrFail($id);

        return response()->json($tailor);
    }



    public function updateTailor(Request $request, $id)
    {

        $tailor = Tailor::findOrFail($id);

        $tailor->update([
            'shop_name' => $request->shop_name,
            'description' => $request->description,
            'experience_years' => $request->experience_years
        ]);

        return response()->json([
            'message' => 'Tailor updated'
        ]);
    }

    public function tailorServices($tailor_id)
    {

        return response()->json(
            TailorService::where('tailor_id', $tailor_id)->get()
        );
    }

    /*
    =========================
    Tailor Service APIs
    =========================
    */

    public function addService(Request $request)
    {

        $service = TailorService::create([
            'tailor_id' => $request->tailor_id,
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Service added',
            'data' => $service
        ]);
    }

    public function updateService(Request $request, $id)
    {

        $service = TailorService::findOrFail($id);

        $service->update([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Service updated'
        ]);
    }

    public function deleteService($id)
    {

        TailorService::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Service deleted'
        ]);
    }

    /*
    =========================
    Portfolio APIs
    =========================
    */

    public function portfolio($tailor_id)
    {

        return response()->json(
            TailorPortfolio::where('tailor_id', $tailor_id)->get()
        );
    }

    public function uploadPortfolio(Request $request)
    {

        $image = $request->file('image')->store('portfolio');

        $portfolio = TailorPortfolio::create([
            'tailor_id' => $request->tailor_id,
            'image' => $image,
            'title' => $request->title
        ]);

        return response()->json([
            'message' => 'Portfolio uploaded',
            'data' => $portfolio
        ]);
    }

    /*
    =========================
    Availability APIs
    =========================
    */

    public function availability(Request $request)
    {

        try {
            $search   = $request->search;
            $status   = $request->status; // 1 = available, 0 = unavailable
            $perPage  = $request->per_page ?? 10;

            $query = TailorAvailability::where('tailor_id', authUserId());

            // 🔍 Search (day or time)
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('day', 'LIKE', "%{$search}%")
                        ->orWhere('start_time', 'LIKE', "%{$search}%")
                        ->orWhere('end_time', 'LIKE', "%{$search}%");
                });
            }

            // 🔄 Status filter
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // 📄 Pagination
            $availability = $query->orderBy('day')->paginate($perPage);

            return response()->json([
                'status'  => true,
                'message' => 'Tailor availability fetched successfully',
                'data'    => $availability
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while fetching availability',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function addAvailability(Request $request)
    {
      
    dd(888);
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time'
        ]);

        if ($validator->fails()) {
            return (new ResponseService())->error(
                'Validation error',
                422,
                ['errors' => $validator->errors()]
            );
        }

        try {
            $tailorId = authUserId();

            // 🔒 Prevent overlapping time slots
            $exists = TailorAvailability::where('tailor_id', $tailorId)
                ->where('day_of_week', $request->day_of_week)
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($q2) use ($request) {
                            $q2->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->exists();

            if ($exists) {
                return (new ResponseService())->error(
                    'Time slot already exists or overlaps',
                    409
                );
            }

            // ✅ Create availability
            $availability = TailorAvailability::create([
                'tailor_id'    => $tailorId,
                'day_of_week'  => $request->day_of_week,
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
                'is_available' => 1
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Availability added successfully',
                'data'    => $availability
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while adding availability',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateAvailability(Request $request, $id)
    {

        $availability = TailorAvailability::findOrFail($id);

        $availability->update([
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->is_available
        ]);

        return response()->json([
            'message' => 'Availability updated'
        ]);
    }

    public function deleteAvailability($id)
    {

        TailorAvailability::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Availability deleted'
        ]);
    }
}
