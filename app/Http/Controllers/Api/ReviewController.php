<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{

    public function reviews($design_id)
    {

        return response()->json(
            Review::where('design_id',$design_id)->get()
        );

    }

    public function addReview(Request $request)
    {

        $review = Review::create([
            'customer_id'=>$request->customer_id,
            'design_id'=>$request->design_id,
            'rating'=>$request->rating,
            'review'=>$request->review
        ]);

        return response()->json([
            'message'=>'Review added',
            'data'=>$review
        ]);

    }

    public function deleteReview($id)
    {

        Review::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Review deleted'
        ]);

    }

}