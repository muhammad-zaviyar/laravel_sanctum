<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

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

            $token = $user->createToken('Api Token of' . $user->name)->plainTextToken;

            return apiResponse(
                [
                    'user' => $userResource,
                    'token' => $token
                ],
                'success',
                'You are logged In Successfully'
            );

        } catch (\Exception $e) {

            $message  = $e->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return success([
            'message' => 'You have successfully been logged out and your token has been deleted',
        ]);
    }
}
