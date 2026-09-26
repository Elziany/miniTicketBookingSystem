<?php
namespace App\Services;

use App\Repositories\UserRepository;

class UserService{

    public function __construct(private UserRepository $userRepository){
    }
    public function getStaffUsers($exceptId = null){
        return $this->userRepository->getStaffUsers($exceptId);
    }
}