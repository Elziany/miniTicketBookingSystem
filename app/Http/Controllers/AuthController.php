<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Http\Requests\UserRegisteration;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }
    public function register(UserRegisteration $request)
    {
        $response = $this->authService->userRegistration($request);
        
        return response()->json($response, 201);
    }

    public function login(UserLogin $request)
    {
        $data = [
            "email" => $request->email ,
            "password" => $request->password
        ];
        $response = $this->authService->login($data);

        return response()->json($response , 200);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
