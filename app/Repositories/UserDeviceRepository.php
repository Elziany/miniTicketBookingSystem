<?php
namespace App\Repositories;

use App\Models\UserDevice;

class UserDeviceRepository {
    public function registerDevice(array $data){
        return UserDevice::create($data);
    }

    public function getUserDevices($userId){
        return UserDevice::where('user_id' , $userId)->get();
    }


    public function deleteDevice(UserDevice $device){
        $device->delete();
    }
}