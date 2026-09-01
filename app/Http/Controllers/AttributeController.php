<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\AttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttributeController extends Controller
{
    public function __construct(
        private AttributeService $service
    ) { }

    public function getAll(): JsonResponse
    {
        return response()->json($this->service->findAll());
    }

    public function getById(string $key): JsonResponse
    {
        try {
            return response()->json($this->service->findById($key));
        } catch (NotFoundException) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json($this->service->create($request->all()), 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function update(Request $request, string $key): JsonResponse
    {
        try {
            return response()->json($this->service->update($key, $request->all()));
        } catch (NotFoundException) {
            return response()->json(['message' => 'Attribute not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function delete(string $key): JsonResponse
    {
        try {
            $this->service->delete($key);
            return response()->json(null, 204);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }
    }
}
