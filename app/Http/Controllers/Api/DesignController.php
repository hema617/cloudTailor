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
use App\Services\ResponseService;
use Illuminate\Support\Facades\Validator;


class DesignController extends Controller
{

    /*
    =========================
    Category APIs
    =========================
    */

    public function categories(Request $request)
    {
        try {
            $search   = $request->search;
            $status   = $request->status;
            $perPage  = $request->per_page ?? 10;

            $query = Category::query()->with('subcategories');

            // 🔍 Search (name or any column you want)
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            // 🔄 Status filter
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // 📄 Pagination
            $categories = $query->paginate($perPage);

            return (new ResponseService())->success(
                'Category List fetched successfully',
                $categories
            );
        } catch (\Exception $e) {
            return (new ResponseService())->error(
                'Error while fetching Category ',
                500,
                ['error'   => $e->getMessage()]
            );
        }
    }

    public function designs(Request $request)
    {
        try {
            $search   = $request->search;
            $status   = $request->status;
            $perPage  = $request->per_page ?? 10;

            $query = Design::with(['images', 'tailor'])->latest();

            // 🔍 Search (title, description, tailor name)
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('tailor', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // 🔄 Status filter
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // 📄 Pagination
            $designs = $query->paginate($perPage);
            return (new ResponseService())->success(
                'Design list fetched successfully',
                $designs
            );
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error while fetching designs',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /*
    =========================
    Tailors APIs
    =========================
    */


    public function addDesign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'    => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'tailor_id'      => 'required|integer|exists:users,id',
            'name'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0'
        ]);

        // ❌ Validation fail
        if ($validator->fails()) {
            return (new ResponseService())->error(
                'Validation error',
                422,
                ['errors'  => $validator->errors()]
            );
        }

        try {
            $design = Design::create([
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'tailor_id'      => $request->tailor_id,
                'name'          => $request->name,
                'description'    => $request->description,
                'price'          => $request->price,
                'status'         => 0
            ]);

            return response()->json([
                'message' => 'Design added successfully',
                'data'    => $design
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function updateDesign(Request $request)
    {
        $id = $request->route('id');
        $validator = Validator::make($request->all(), [
            'category_id'    => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'status'         => 'nullable|in:0,1'
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

            // 🔍 Find design (only own design)
            $design = Design::where('id', $id)
                ->where('tailor_id', $user->id)
                ->first();

            if (!$design) {
                return (new ResponseService())->error(
                    'Design not found or unauthorized',
                    404
                );
            }

            // ✅ Update
            $design->update([
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name'           => $request->name,
                'description'    => $request->description,
                'price'          => $request->price,
                'status'         => $request->status ?? $design->status
            ]);

            return response()->json([
                'message' => 'Design updated successfully',
                'data'    => $design
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while updating design',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function updateDesignStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'design_id' => 'required|integer|exists:designs,id',
            'status'    => 'required|in:0,1' // 0 = inactive, 1 = active
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

            // 🔍 Find only logged-in tailor's design
            $design = Design::where('id', $request->design_id)
                ->where('tailor_id', $user->id)
                ->first();

            if (!$design) {
                return (new ResponseService())->error(
                    'Design not found or unauthorized',
                    404
                );
            }

            // ✅ Update status
            $design->update([
                'status' => $request->status
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Design status updated successfully',
                'data'    => $design
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while updating status',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function tailorDesign(Request $request)
    {
        try {
            

            $search  = $request->search;
            $status  = $request->status;
            $perPage = $request->per_page ?? 10;

            $query = Design::with(['images', 'tailor']);
            if (routePrefix() == 'tailor') {
                $query->where('tailor_id', authUserId());
            }
            if (routePrefix() == 'customer') {
                $query->where('status', 1);
            }

            // 🔍 Search
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // 🔄 Status filter
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // 📄 Pagination
            $designs = $query->paginate($perPage);

            return response()->json([
                'status'  => true,
                'message' => 'Tailor design list fetched successfully',
                'data'    => $designs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while fetching designs',
                'error'   => $e->getMessage()
            ], 500);
        }
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
            Subcategory::where('category_id', $category_id)->get()
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
            Design::where('category_id', $category_id)->get()
        );
    }

    public function designsBySubcategory($subcategory_id)
    {
        return response()->json(
            Design::where('subcategory_id', $subcategory_id)->get()
        );
    }

    public function designsByTailor($tailor_id)
    {
        return response()->json(
            Design::where('tailor_id', $tailor_id)->get()
        );
    }





    public function deleteDesign($id)
    {

        Design::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Design deleted'
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
            'design_id' => $request->design_id,
            'image' => $image
        ]);

        return response()->json([
            'message' => 'Image uploaded',
            'data' => $designImage
        ]);
    }

    public function deleteDesignImage($id)
    {

        DesignImage::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Image deleted'
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
                ->where('design_id', $design_id)
                ->get()
        );
    }

    public function addOption(Request $request)
    {

        $option = DesignOption::create([
            'design_id' => $request->design_id,
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Option added',
            'data' => $option
        ]);
    }

    public function updateOption(Request $request, $id)
    {

        $option = DesignOption::findOrFail($id);

        $option->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Option updated'
        ]);
    }

    public function deleteOption($id)
    {

        DesignOption::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Option deleted'
        ]);
    }
}
