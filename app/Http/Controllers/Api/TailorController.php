<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tailor;
use App\Models\TailorService;
use App\Models\TailorPortfolio;
use App\Models\TailorAvailability;

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

    public function registerTailor(Request $request)
    {

        $tailor = Tailor::create([
            'user_id'=>$request->user()->id,
            'shop_name'=>$request->shop_name,
            'description'=>$request->description,
            'experience_years'=>$request->experience_years,
            'status'=>1
        ]);

        return response()->json([
            'message'=>'Tailor registered',
            'data'=>$tailor
        ]);
    }

    public function updateTailor(Request $request,$id)
    {

        $tailor = Tailor::findOrFail($id);

        $tailor->update([
            'shop_name'=>$request->shop_name,
            'description'=>$request->description,
            'experience_years'=>$request->experience_years
        ]);

        return response()->json([
            'message'=>'Tailor updated'
        ]);
    }

    public function tailorServices($tailor_id)
    {

        return response()->json(
            TailorService::where('tailor_id',$tailor_id)->get()
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
            'tailor_id'=>$request->tailor_id,
            'name'=>$request->name,
            'price'=>$request->price
        ]);

        return response()->json([
            'message'=>'Service added',
            'data'=>$service
        ]);
    }

    public function updateService(Request $request,$id)
    {

        $service = TailorService::findOrFail($id);

        $service->update([
            'name'=>$request->name,
            'price'=>$request->price
        ]);

        return response()->json([
            'message'=>'Service updated'
        ]);
    }

    public function deleteService($id)
    {

        TailorService::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Service deleted'
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
            TailorPortfolio::where('tailor_id',$tailor_id)->get()
        );
    }

    public function uploadPortfolio(Request $request)
    {

        $image = $request->file('image')->store('portfolio');

        $portfolio = TailorPortfolio::create([
            'tailor_id'=>$request->tailor_id,
            'image'=>$image,
            'title'=>$request->title
        ]);

        return response()->json([
            'message'=>'Portfolio uploaded',
            'data'=>$portfolio
        ]);
    }

    /*
    =========================
    Availability APIs
    =========================
    */

    public function availability($tailor_id)
    {

        return response()->json(
            TailorAvailability::where('tailor_id',$tailor_id)->get()
        );
    }

    public function addAvailability(Request $request)
    {

        $availability = TailorAvailability::create([
            'tailor_id'=>$request->tailor_id,
            'day_of_week'=>$request->day_of_week,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'is_available'=>1
        ]);

        return response()->json([
            'message'=>'Availability added',
            'data'=>$availability
        ]);
    }

    public function updateAvailability(Request $request,$id)
    {

        $availability = TailorAvailability::findOrFail($id);

        $availability->update([
            'day_of_week'=>$request->day_of_week,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'is_available'=>$request->is_available
        ]);

        return response()->json([
            'message'=>'Availability updated'
        ]);
    }

    public function deleteAvailability($id)
    {

        TailorAvailability::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Availability deleted'
        ]);
    }

}