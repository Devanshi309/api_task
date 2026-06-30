<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notifications(Request $request)
    {
        if ($request->user()->role != 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

         return response()->json([
             'status' => true,
             'notifications' => $request->user()->notifications
        ]);
    }
}
