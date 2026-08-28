<?php

namespace App\Services;

use App\Exceptions\InvalidActionException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserService $userService
    ) { }

    /**
     * Register an admin user.
     *
     * @param array $data
     * @return mixed
     * @throws InvalidActionException
     */
    public function registerAdmin(array $data)
    {
        if ($this->userService->countAll() > 0) {
            throw new InvalidActionException("Admin user already exists.");
        }

        return $this->userService->create($data);
    }

    public function login(array $data): string
    {
        $validator = Validator::make($data, [
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->userService->findByEmail($validator->validated()['email']);
        if (!$user || !Hash::check($validator->validated()['password'], $user->password)) {
            throw new InvalidCredentialsException("Email and/or Password do not match.");
        }

        return $user->createToken("authentication")->plainTextToken;
    }
}