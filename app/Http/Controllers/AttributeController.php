<?php

namespace App\Http\Controllers;

use App\Core\Attribute\AttributeKey;
use App\Core\Attribute\CreateAttribute;
use App\Core\Attribute\UpdateAttribute;
use App\Exceptions\NotFoundException;
use App\Services\AttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            $attribute = $this->service->findByKey(new AttributeKey($key));
            return response()->json($attribute);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $validated = Validator::validate($request->all(), [
                'key' => 'required|string|max:50|unique:attributes,key',
                'label' => 'required|string|max:255|unique:attributes,label',
                'description' => 'nullable|string',
            ]);

            $created = $this->service->create(
                new CreateAttribute(
                    $validated['key'],
                    $validated['label'],
                    $validated['description'] ?? null
                )
            );

            return response()->json($created, 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function update(Request $request, string $key): JsonResponse
    {
        try {
            $validated = Validator::validate($request->all(), [
                'label' => 'sometimes|string|max:255|unique:attributes,label,' . $key . ',key',
                'description' => 'nullable|string',
            ]);
            
            $updated = $this->service->update(
                new AttributeKey($key),
                new UpdateAttribute(
                    $validated['label'] ?? '',
                    $validated['description'] ?? null
                )
            );

            return response()->json($updated);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function delete(string $key): JsonResponse
    {
        try {
            $this->service->delete(new AttributeKey($key));
            return response()->json(null, 204);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }
}
