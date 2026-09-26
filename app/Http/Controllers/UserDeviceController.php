<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDevice\RegisterDevice;
use App\Services\UserDeviceService;
use Illuminate\Http\Request;

class UserDeviceController extends Controller
{
    public function __construct(private UserDeviceService $userDeviceService)
    {
    }
    public function registerDevice(RegisterDevice $request){
         $deviceData = [
            'user_id' => $request->user()->id,
            'platform' => $request->platform , 
            'device_token' => $request->device_token
        ];
        $device = $this->userDeviceService->registerDevice($deviceData);
        return response()->json([
            "device" => $device , 
            "message" => "device registered successfully"
        ] , 201);
    }
}
