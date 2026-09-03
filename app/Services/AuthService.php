<?php

namespace App\Services;

use App\Core\Auth\Credentials;
use App\Core\Auth\PasswordHasher;
use App\Core\User\CreateUser;
use App\Exceptions\ForbiddenException;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserService $userService
    ) { }

    /**
     * Register an admin user.
     *
     * @param CreateUser $data
     * @return mixed
     * @throws ForbiddenException
     */
    public function registerAdmin(CreateUser $data)
    {
        if ($this->userService->countAll() > 0) {
            throw new ForbiddenException("Admin user already exists.");
        }

        return $this->userService->create($data);
    }

    /**
     * Login a user with email and password.
     *
     * @param Credentials $data
     * @return string
     * @throws ValidationException
     * @throws InvalidCredentialsException
     */
    public function login(Credentials $data): string
    {
        $hasher = new PasswordHasher();
        $user = $this->userService->findByEmail($data->email);

        if (!$user || ! $hasher->check($user->password, $data->password)) {
            throw new InvalidCredentialsException("Email and/or Password do not match.");
        }

        return $user->createToken("authentication")->plainTextToken;
    }
}