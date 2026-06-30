<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Validator;
use App\Models\cart;
use App\Models\Order;
use App\Models\delivery;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
        public function placeorder(Request $request)
        {
             $user=Auth::user();
             if($user->role != 'customer')
                {
                    return response()->json([
                        'status' => false,
                        'message' => 'only customers can place order'
                    ],403);
                }
                $cartItems=Cart::with('product')->where('user_id',$user->id)->get();
                // dd($cartItems);
                if($cartItems->isEmpty())
                    {
                       return response()->json([
                            'status' => false,
                            'message' => 'cart is empty'
                       ],400); 
                    }
                    DB::beginTransaction();
                    try{
                        $totalAmount=0;
                        foreach($cartItems as $item)
                            {
                               $totalAmount += $item->quantity * $item->product->price; 
                               if($item->quantity <= $item->product->Stock_quantity)
                                {
                                        $item->product->Stock_quantity -= $item->quantity;
                                        $item->product->save();
                                }
                                else{
                                    DB::rollback();
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'order quantity is greater than stock quantity'
                                    ]);
                                }
                            } 
                        $validator= Validator::make($request->all(),[
                                'shipping_address' => 'required|string',
                                'payment_method' => 'required'
                        ]);
                        if($validator->fails())
                            {
                                return response()->json([
                                    'status' => false,
                                    'error' => $validator->errors()
                                ]);
                            }
                             $order=Order::create([
                                'user_id' => $user->id,
                                'total_amount' => $totalAmount,
                                'status' => 'pending',
                                'shipping_address' => $request->shipping_address,
                                'payment_method'=>$request->payment_method
                            ]);
                            //dd($order);
                            foreach($cartItems as $item)
                                {
                                    OrderItem::create([
                                        'order_id'=>$order->id,
                                        'grocery_item_id'=> $item->grocery_item_id,
                                        'quantity'=>$item->quantity,
                                        'price'=>$item->product->price,
                                        'subtotal'=>$item->quantity * $item->product->price,
                                    ]);
                                }
                               
                               // dd(cart::where('user_id',$user->id));
                                Cart::where('user_id',$user->id)->delete();
                                DB::commit();
                                 $order->load('user');
                                $admin = User::where('role', 'admin')->first();
                                 if ($admin) {
                                    $admin->notify(new OrderPlacedNotification($order));
                                }

                                return response()->json([
                                    'status' =>true,
                                    'message' => 'order placed successfully',
                                    'order_id' => $order->id,
                                    'total_amount'=> $totalAmount
                                ]);
                    }
                    catch (\Exception $e){
                            DB::rollback();
                            return response()->json([
                                'status'=> false,   
                                'message'=>$e->getMessage()
                            ],500);
                    }
        }
        public function orderlist()
        {
               
                $order=Order::latest()->get();
                $data = $order->map(function ($order)
                {
                    return[
                        'id'=>$order->id,
                        'user_id'=>$order->user_id,
                        'total_amount'=>$order->total_amount,
                        'status'=>$order->status
                    ];
                });
                return response()->json([
                    'status' => true,
                    'message' => 'order list',
                    'order' => $data
                ]);
        }  
        public function orderdetail(Request $request)
        {
                $detail=Order::where('id',$request->id)->first();
                return response()->json([
                    'status' => true,
                    'message' => 'order detail',
                    'detail' => $detail
                ]);
        }
        public function statusupdate(Request $request,$id)
        {
                $validator=Validator::make($request->all(),[
                        'status' => 'required|in:pending,confirmed,delivered,cancelled'
                ]);
                if($validator->fails())
                    {
                        return response()->json([
                            'status' => false,
                            'error' => $validator->errors()
                        ],422);
                    }
                $order=Order::find($id);
                $order->update([
                    'status'=>$request->status
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'status updated',
                    'status' => [
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'status' => $order->status
                    ]
                ]);
        }
}
