<?php

namespace App\Http\Controllers;

use App\Core\Auth\PlainPassword;
use App\Core\User\CreateUser;
use App\Core\User\UserID;
use App\Core\User\UpdateUser;
use App\Enums\UserRole;
use App\Exceptions\NotFoundException;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(
        private UserService $service
    ) { }

    public function getAll(): JsonResponse
    {
        return response()->json($this->service->findAll());
    }

    public function getById(string $id): JsonResponse
    {
        try {
            $user = $this->service->findById(new UserID($id));
            return response()->json($user);
        } catch (NotFoundException) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = Validator::validate($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:' . UserRole::toStringList(),
            ]);

            $user = $this->service->create(
                new CreateUser(
                    $validated['name'],
                    $validated['email'],
                    new PlainPassword($validated['password']),
                    UserRole::from($validated['role'])
                )
            );

            DB::commit();
            return response()->json($user, 201);
        } catch (ValidationException $exception) {
            DB::rollBack();
            return response()->json(['errors' => $exception->errors()], 422);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = Validator::validate($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'currentPassword' => 'required|string|min:8',
            ]);

            $user = $this->service->update(
                new UserID($id),
                new UpdateUser(
                    $validated['name'],
                    $validated['email'],
                    new PlainPassword($validated['currentPassword'])
                )
            );

            return response()->json($user);
        } catch (NotFoundException) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->service->delete(new UserID($id));
            return response()->json(null, 204);
        } catch (NotFoundException) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}