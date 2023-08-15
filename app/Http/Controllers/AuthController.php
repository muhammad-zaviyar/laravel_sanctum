<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(LoginUserRequest $request)
    {
        try {

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return apiResponse(null, 'failed', 'Incorrect Email or Password', 401);
            }

            $user = $this->userRepository->findByEmail($credentials['email']);

            $userResource = new UserResource($user);

            $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;

            return apiResponse(
                [
                    'user' => $userResource,
                    'token' => $token
                ],
                'successful',
                'You are logged In Successfully'
            );

        } catch (\Exception $e) {

            $message  = $e->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }
    }

    public function registration(StoreUserRequest $request)
    {
        try {
            $user = $this->userRepository->createUser($request->all());

            $userResource = new UserResource($user);

            $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;

            return apiResponse(
                [
                    'user' => $userResource,
                    'token' => $token
                ],
                'successful',
                'Your account has been registered successfully'
            );

        } catch (\Exception $e) {

            $message  = $e->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        $message = 'You have successfully been logged out and your token has been deleted';

        return apiResponse(null, 'success', $message, 500);
    }
}
