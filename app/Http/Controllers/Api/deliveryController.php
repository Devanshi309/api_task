<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\cart;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\delivery;

class deliveryController extends Controller
{
    public function assigndelivery(Request $request,$id)
    {
            $validator= Validator::make($request->all(),[
                    'order_id' => 'required|exists:orders,id',
                    'status' => 'required|in:assigned,out_for_delivery,delivered,failed'
            ]);
            if($validator->fails())
            {
                return response()->json([
                    'status' => false,
                    'error' => $validator ->errors()
                ],422);
            }
        $delivery=delivery::create([
            'order_id'=>$request->order_id,
            'delivery_boy_name'=>$request->delivery_boy_name,
            'status'=> $request->status,
            'delivery_date'=>now()->toDateString()
        ]);
        return response()->json([
            'status' => true,
            'message' => 'order assigned successfully',
            'delivery' => $delivery
        ]);
    }
    public function dailydelivery()
    {
            $daily=delivery::whereDate('delivery_date',today())->get();
            return response()->json([
                'status' => true,
                'deliveries' => $daily
            ]);
    }
    public function updatestatus(Request $request,$id)
    {
        $validator=Validator::make($request->all(),[
                        'status' => 'required|in:assigned,out_for_delivery,delivered,failed'
        ]);
        if($validator->fails())
            {
                return response()->json([
                    'status' => false,
                    'error' => $validator ->errors()
                ],422);
            }
        $delivery=Delivery::find($id);
        if(!$delivery)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'delivery not found'
                ]);
            }
            $delivery->status = $request->status;
            $delivery->save();
            if($request->status == 'delivered')
                {
                    $delivery->order->update([
                        'status' => 'delivered'
                    ]);
                }
                return response()->json([
                        'status' => true,
                        'message' => 'delivery status updated' 
                ]);
    }
    public function dailyreport(Request $request)
    {
            $todaysale = Order::whereDate('created_at',Carbon::today())->sum('total_amount'); 
            return response()->json([
                'status' => true,
                'daily sales report' => $todaysale
            ]);
    }
    public function monthreport(Request $request)
    {
        $monthsale= Order::whereMonth('created_at',Carbon::now()->month)->whereYear('created_at',Carbon::now()->year)->sum('total_amount');
        return response()->json([
            'status' => true,
            'monthly sales report' => $monthsale
        ]);
    }
    public function customerpurchasereport(Request $request,$id)
    {   
          $admin = Auth::user();
         if ($admin->role != 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can view customer purchase reports.'
                ], 403);
            }
             $customer = User::find($id);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.'
                ], 404);
            } 
             $orders = Order::with('items.product')->where('user_id', $customer->id)->get();

    return response()->json([
        'status' => true,
        'customer_name' => $customer->name,
        'total_orders' => $orders->count(),
        'total_purchase_amount' => $orders->sum('total_amount'),
        'orders' => $orders
    ]);          
 }
    
}
