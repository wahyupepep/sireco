<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class NotificationController extends Controller
{
    public function getData()
    {
        try {
            $notifications = Notification::select('id', 'text', 'reservation_id', 'user_id')
                ->with('user')
                ->where([
                    'read' => 0,
                    'user_id' => Auth::user()->id
                ])
                ->whereHas('user')
                ->get();

            $notification = $notifications->map(function ($value) {
                $data['id'] = $value->id;
                $data['text'] = $value->text;
                $data['reservation_id'] = Crypt::encryptString($value->reservation_id);
                $data['is_member'] = $value->user->role == 4 ? true : false;
                return $data;
            });
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'OK',
                'data' => [
                    'notifications' => $notification
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $th->getMessage() . " on the line " . $th->getLine()
            ], 500);
        }
    }
}
