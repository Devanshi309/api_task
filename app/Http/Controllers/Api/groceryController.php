<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\grocery_item;
use Illuminate\Http\Request;
use App\Http\Resources\GroceryItemResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class groceryController extends Controller
{
    public function store(Request $request)
    {
     
        $validator=Validator::make($request->all(),[
            'product_name'=>'required|string',
            'catgory_id'=>'nullable|integer',
            'image'=>'required|image|mimes:png,jpg',
            'price'=>'required|integer',
            'Stock_quantity' => 'required|integer',
            'expiry_date' => 'required|date',
            'description' => 'required|string|max:255',
            'status' => 'required|in:pending,confirmed,delivered,cancelled'
        ]);
        if($validator->fails())
            {
                return response()->json([
                    'status' => false,
                    'error' => $validator ->errors()
                ],422);
            }
            if($request->hasFile('image'))
                {
                    $pname=$request->product_name;
                    $imagename=$pname.'.'.$request->image->getClientOriginalExtension();
                    $request->image->move(public_path('upload'),$imagename);
                }
            $add=grocery_item::create([
                    'product_name'=> $request->product_name,
                    'catgory_id'=>$request->catgory_id,
                    'image'=>$imagename,
                    'price'=>$request->price,
                    'Stock_quantity'=>$request->Stock_quantity,
                    'expiry_date'=>$request->expiry_date,
                    'description'=>$request->description,
                    'status'=>$request->status
            ]);
            return response()->json([
                'status' => true,
                'message' => 'product add successfully',
                'grocery_item' => $add
            ],201);
    }
    public function update(Request $request,$id)
    {
       // dd($request->all());
        
            $product=grocery_item::where('id',$id)->first();
            //dd($product);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }
             $validator= Validator::make($request->all(),[

            'product_name'=>'string',
            'catgory_id'=>'nullable|integer',
            'image'=>'image|mimes:png,jpg',
            'price'=>'integer',
            'Stock_quantity' => 'integer',
            'expiry_date' => 'date',
            'description' => 'string|max:255',
            'status' => 'in:pending,confirmed,delivered,cancelled'
             ]);
             if($validator->fails())
                {
                    return response()->json([
                        'status' => false,
                        'error' => $validator->errors()
                    ],422);
                }
            if($request->hasFile('image'))
            {
                    $pname = $request->product_name ?? $product->product_name;
                    $imagename=$pname.'.'.$request->image->getClientOriginalExtension();
                    $request->image->move(public_path('upload'),$imagename);
            }
            $product->product_name = $request->product_name ?? $product->product_name;
            $product->catgory_id = $request->catgory_id ?? $product->catgory_id;
            $product->price = $request->price ?? $product->price;
            $product->Stock_quantity = $request->Stock_quantity ?? $product->Stock_quantity;
            $product->expiry_date = $request->expiry_date ?? $product->expiry_date;
            $product->description = $request->description ?? $product->description;
            $product->status = $request->status ?? $product->status;
            $product->save();
            return response()->json([
                'status'=>true,
                'message' =>'update successfully',
                'product' => $product
            ]);
    }
    public function delete($id)
    {
        
        $product=grocery_item::find($id);
        if(!$product)
            {
               return response()->json([
                    'status' => false,
                    'message' => 'product not deleted'
               ],404); 
            }
            $product->delete();
            return response()->json([
                'status' => true,
                'message' => 'product delete successfully'
            ]);
    }
    public function list(Request $request)
    {
            $list=grocery_item::all();
            return response()->json([
                'status' => true,
                'message' => 'list of grocery items',
                'list' =>  GroceryItemResource::collection($list)
            ]);
    }
    public function detail(Request $request,$id)
    {
            
            $detail=grocery_item::find($id);
            if (!$detail) {
             return response()->json([
            'status' => false,
            'message' => 'Product not found'
        ], 404);
    }
            return response()->json([
                'status' => true,
                'message' => 'grocery item detail fetch successfully',
                'detail' => new GroceryItemResource($detail)
            ]);
    }
    
}
