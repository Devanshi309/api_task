<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Resources\GroceryItemResource;
use Illuminate\Support\Facades\Auth;
use App\Models\cart;
use App\Models\User;
use App\Models\delivery;
use App\Models\grocery_item;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
     public function addtocart(Request $request)
    {
            $user = Auth::user();

        if($user->role != 'customer')
        {
            return response()->json([
                'status' => false,
                'message' => 'Only customers can add products to cart'
            ],403);
        }
        $validator=Validator::make($request->all(),[
             'grocery_item_id' => 'required|exists:grocery_items,id',
            'quantity' => 'required|integer|min:1'
        ]);
        if($validator->fails())
            {
                return response()->json([
                    'status' => false,
                    'error' => $validator->fails()
                ]);
            }
        $product=grocery_item::find($request->grocery_item_id);
    
        if(!$product)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'product not found or has been deleted'
                ],404);
            }
        $cart=cart::where('user_id',$user->id)
        ->where('grocery_item_id',$request->grocery_item_id)->first();
        if($cart)
            {
                $cart->quantity += $request->quantity;
                $cart->save();
            }
            else{
                $cart= cart::create([
                    'user_id' => $user->id,
                    'grocery_item_id'=> $request->grocery_item_id,
                    'quantity' => $request->quantity,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'product added to cart',
                'cart' =>$cart
            ]);

    }
    public function updatecart(Request $request,$id)
    {
        $cart=cart::find($id);
        $validator=Validator::make($request->all(),[
            'quantity' => 'required|integer|min:1'
        ]);
        if($validator->fails())
            {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ],422);
            }
             $cart->update([
            'quantity' => $request->quantity
        ]);
        return response()->json([
            'status' => true,
            'message' => 'cart updated'
        ]);
    }
    public function removecart(Request $request,$id)
    {
        $cart=cart::find($id);
        $cart->delete();
        return response()->json([
            'status' => true,
            'message' => 'product remove from cart'
        ]);
    }
    public function dashboardapi()
    {
        $tcustomer=user::where('role','customer')->count();
        $tgrocery=grocery_item::count();
        $torder=order::count();
        $tdelivery=delivery::count();
        return response()->json([
            'status' => true,
            'message' => 'dashboard data successfully fetched',
            'data' => [
                'total customer' => $tcustomer,
                'total grocery Items' => $tgrocery,
                'total orders' => $torder,
                'total Deliveries' => $tdelivery
            ]
        ]);
    }
    public function filter(Request $request)
    {
            $validator=Validator::make($request->all(),[
                'filter' => 'required|in:today,week,month'
            ]);
            if($validator->fails())
                {
                    return response()->json([
                        'status' => false,
                        'error' => $validator->errors()
                    ]);
                }
                 if($request->filter == 'today')
                {
                   $tcustomer=user::where('role','customer')->whereDate('created_at', Carbon::today())->count();
                   $tgrocery=grocery_item::whereDate('created_at', Carbon::today())->count();
                   $tdelivery=delivery::whereDate('created_at', Carbon::today())->count();
                }
                if($request->filter == 'week')
                {
                    $tcustomer=user::where('role','customer')->whereBetween('created_at', [
                             Carbon::now()->startOfWeek(),
                             Carbon::now()->endOfWeek()])->count();
                    $tgrocery=grocery_item::whereBetween('created_at', [ Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $tdelivery=delivery::whereBetween('created_at', [
                             Carbon::now()->startOfWeek(),
                             Carbon::now()->endOfWeek()])->count();
                }
                if($request->filter == 'month')
                    {
                        $tcustomer=user::where('role','customer')->whereMonth('created_at', Carbon::now()->month)->count();
                        $tgrocery=grocery_item::whereMonth('created_at', Carbon::now()->month)->count();
                        $tdelivery=delivery::whereMonth('created_at', Carbon::now()->month)->count();
                    }
                   // $customer=$tcustomer->get();
                    return response()->json([
                        'status' => true,
                        'total customers' => $tcustomer,
                        'total grocery_items' => $tgrocery,
                        'total delivery' => $tdelivery
                    ]);
    }
    public function viewproduct(Request $request)
    {
         $user = Auth::user();

    if($user->role!='customer')
    {
        return response()->json([
            'status' => false,
            'message' => 'Only customers can add products to cart'
        ],403);
    }
   // dd('admin');
        $view=grocery_item::all();
        $products = $view->map(function ($item) {
        return [
            'product_name' => $item->product_name,
            'price' => $item->price,
            'description' => $item->description,
            'image' => $item->image,
        ];
    });
        return response()->json([
        'status' => true,
        'message' => 'Grocery products',
        'view' => $products
    ]);
    }
}
