<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidActionException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\NotFoundException;
use App\Services\AuthService;
use Exception;
use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private UserService $userService
    ) { }

    public function registerAdmin(Request $request)
    {
        try {
            $user = $this->authService->registerAdmin($request->all());
            return response()->json($user, 201);
        } catch (InvalidActionException $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $token = $this->authService->login($request->all());
            return response()->json(['token' => $token], 200);
        } catch (InvalidCredentialsException $e) {
            return response()->json([ 'message' => $e->getMessage() ], 401);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        try {
            return response()->json($this->userService->findById($user->id), 200);
        } catch (NotFoundException $e) {
            return response()->json([ 'message' => $e->getMessage() ], 404);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        }
    }
}