<?php
namespace App\Repositories;

use App\Enum\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository {
    public function getUserByEmail($email):User{
        return User::where('email' , $email)->first();
    }

    public function createUser($userData):User{
        $user = User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'type' => $userData->type ?? UserType::GUEST,
            'phone' => $userData->phone
        ]);
        return $user ;
    }

    public function getStaffUsers($exceptId = null){
        $query = User::where('type', UserType::STAFF);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        return $query->get();
    }
}