<?php
namespace App\Services;

use App\Models\UserDevice;
use App\Repositories\UserDeviceRepository;

class UserDeviceService {
    public function __construct(private UserDeviceRepository $userDeviceRepository){

    }
    public function registerDevice(array $data): UserDevice{
        
        return $this->userDeviceRepository->registerDevice($data);
    }

    public function getUserDevices($userId){
        return $this->userDeviceRepository->getUserDevices($userId);
    }
    
    public function deleteDevice($device){
        $this->userDeviceRepository->deleteDevice($device);
    }
    
}