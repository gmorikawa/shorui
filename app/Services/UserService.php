<?php

namespace App\Services;

use App\Core\Auth\PasswordHasher;
use App\Core\User\CreateUser;
use App\Core\User\UserID;
use App\Core\User\UpdateUser;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        private readonly FolderService $folderService
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

    /**
     * Count all users.
     *
     * @return int
     */
    public function countAll(): int
    {
        return User::count();
    }

    /**
     * Find a user by ID.
     *
     * @param UserID $id
     * @return User|null
     */
    public function findById(UserID $id): ?User
    {
        $user = User::find($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        return $user->load('folder');
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
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
     * @param CreateUser $data
     * @return User
     */
    public function create(CreateUser $data): User {
        $hasher = new PasswordHasher();

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $hasher->hash($data->password),
            'role' => $data->role->value,
        ]);

        $folder = $this->folderService->create($user->name, $user, null);
        $user->folder()->associate($folder);
        $user->save();

        return $user->load('folder');
    }

    /**
     * Update a user by ID.
     * To update user's information the current password must be provided for verification.
     *
     * @param UserID $id
     * @param UpdateUser $data
     * @return User|null updated user
     * @throws NotFoundException if the user with the given ID does not exist
     * @throws ForbiddenException if the current password is incorrect
     */
    public function update(UserID $id, UpdateUser $data): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        $hasher = new PasswordHasher();
        if (!$hasher->check($user->password, $data->currentPassword)) {
            throw new ForbiddenException("Current password is incorrect");
        }

        $user->name = $data->name;
        $user->email = $data->email;
        $user->save();

        return $user->load('folder');
    }

    /**
     * Delete a user by ID.
     *
     * @param UserID $id
     * @throws NotFoundException
     */
    public function delete(UserID $id): void
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new NotFoundException("User with ID $id not found");
        }

        $user->delete();
    }
}
