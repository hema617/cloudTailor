<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Design;
use App\Models\DesignImage;
use App\Models\DesignOption;
use App\Models\DesignOptionValue;

class DesignController extends Controller
{

    /*
    =========================
    Category APIs
    =========================
    */

    public function categories()
    {
        return response()->json(
            Category::where('status',1)->get()
        );
    }

    public function category($id)
    {
        return response()->json(
            Category::findOrFail($id)
        );
    }

    public function subcategories($category_id)
    {
        return response()->json(
            Subcategory::where('category_id',$category_id)->get()
        );
    }

    public function subcategory($id)
    {
        return response()->json(
            Subcategory::findOrFail($id)
        );
    }


    /*
    =========================
    Design APIs
    =========================
    */

    public function designs()
    {
        return response()->json(
            Design::with(['images','tailor'])->latest()->get()
        );
    }

    public function design($id)
    {
        $design = Design::with([
            'images',
            'options.values',
            'tailor'
        ])->findOrFail($id);

        return response()->json($design);
    }

    public function designsByCategory($category_id)
    {
        return response()->json(
            Design::where('category_id',$category_id)->get()
        );
    }

    public function designsBySubcategory($subcategory_id)
    {
        return response()->json(
            Design::where('subcategory_id',$subcategory_id)->get()
        );
    }

    public function designsByTailor($tailor_id)
    {
        return response()->json(
            Design::where('tailor_id',$tailor_id)->get()
        );
    }

    public function addDesign(Request $request)
    {

        $design = Design::create([
            'category_id'=>$request->category_id,
            'subcategory_id'=>$request->subcategory_id,
            'tailor_id'=>$request->tailor_id,
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=>1
        ]);

        return response()->json([
            'message'=>'Design added',
            'data'=>$design
        ]);
    }

    public function updateDesign(Request $request,$id)
    {

        $design = Design::findOrFail($id);

        $design->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price
        ]);

        return response()->json([
            'message'=>'Design updated'
        ]);
    }

    public function deleteDesign($id)
    {

        Design::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Design deleted'
        ]);
    }


    /*
    =========================
    Design Image APIs
    =========================
    */

    public function uploadDesignImage(Request $request)
    {

        $image = $request->file('image')->store('designs');

        $designImage = DesignImage::create([
            'design_id'=>$request->design_id,
            'image'=>$image
        ]);

        return response()->json([
            'message'=>'Image uploaded',
            'data'=>$designImage
        ]);
    }

    public function deleteDesignImage($id)
    {

        DesignImage::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Image deleted'
        ]);
    }


    /*
    =========================
    Design Options APIs
    =========================
    */

    public function designOptions($design_id)
    {
        return response()->json(
            DesignOption::with('values')
            ->where('design_id',$design_id)
            ->get()
        );
    }

    public function addOption(Request $request)
    {

        $option = DesignOption::create([
            'design_id'=>$request->design_id,
            'name'=>$request->name
        ]);

        return response()->json([
            'message'=>'Option added',
            'data'=>$option
        ]);
    }

    public function updateOption(Request $request,$id)
    {

        $option = DesignOption::findOrFail($id);

        $option->update([
            'name'=>$request->name
        ]);

        return response()->json([
            'message'=>'Option updated'
        ]);
    }

    public function deleteOption($id)
    {

        DesignOption::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Option deleted'
        ]);
    }

}