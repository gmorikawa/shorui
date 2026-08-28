<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private FolderService $folderService
    ) { }

    /**
     * Get all users.
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return User::all();
    }

    public function countAll(): int
    {
        return User::count();
    }

    /**
     * Find a user by ID.
     *
     * @param string $id
     * @return User|null
     */
    public function findById(string $id): ?User
    {
        $user = User::find($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        return $user->load('folder');
    }

    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new NotFoundException("User with email $email not found");
        }

        return $user->load('folder');
    }

    /**
     * Create a new user and its home folder.
     *
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function create(array $data): User {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::create($validator->validated());

        $folder = $this->folderService->create($user->name, $user, null);
        $user->folder()->associate($folder);
        $user->save();

        return $user->load('folder');
    }

    /**
     * Update a user by ID.
     *
     * @param string $id
     * @param array $data
     * @return User|null
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(string $id, array $data): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user->update($validator->validated());

        return $user->load('folder');
    }

    /**
     * Delete a user by ID.
     *
     * @param string $id
     * @throws NotFoundException
     */
    public function delete(string $id): void
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        $user->delete();
    }
}