<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $user = $this->service->findById($id);
            return response()->json($user);
        } catch (NotFoundException) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->service->create($request->all());
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
        DB::beginTransaction();
        try {
            $user = $this->service->update($id, $request->all());
            DB::commit();
            return response()->json($user);
        } catch (NotFoundException) {
            DB::rollBack();
            return response()->json(['message' => 'User not found'], 404);
        } catch (ValidationException $exception) {
            DB::rollBack();
            return response()->json(['errors' => $exception->errors()], 422);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function delete(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->delete($id);
            DB::commit();
            return response()->json(null, 204);
        } catch (NotFoundException) {
            DB::rollBack();
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}