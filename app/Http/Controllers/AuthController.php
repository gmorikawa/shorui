<?php

namespace App\Http\Controllers;

use App\Core\Auth\Credentials;
use App\Core\Auth\PlainPassword;
use App\Core\User\CreateUser;
use App\Enums\UserRole;
use App\Exceptions\ForbiddenException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\NotFoundException;
use App\Services\AuthService;
use Exception;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private UserService $userService
    ) { }

    public function registerAdmin(Request $request)
    {
        try {
            $validated = Validator::validate($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $createUser = new CreateUser(
                $validated['name'],
                $validated['email'],
                new PlainPassword($validated['password']),
                UserRole::ADMIN
            );

            $user = $this->authService->registerAdmin($createUser);
            return response()->json($user, 201);
        } catch (ForbiddenException $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = Validator::validate($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            $credentials = new Credentials(
                $validated['email'],
                new PlainPassword($validated['password'])
            );

            $token = $this->authService->login($credentials);
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