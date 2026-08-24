<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(Attribute::all());
    }

    public function getById(string $key): JsonResponse
    {
        $attribute = Attribute::find($key);

        if (!$attribute) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }

        return response()->json($attribute);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key'         => 'required|string|max:50|unique:attributes,key',
            'label'       => 'required|string|max:255|unique:attributes,label',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attribute = Attribute::create($validator->validated());

        return response()->json($attribute, 201);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $attribute = Attribute::find($key);

        if (!$attribute) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label'       => 'sometimes|string|max:255|unique:attributes,label,' . $key . ',key',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attribute->update($validator->validated());

        return response()->json($attribute);
    }

    public function delete(string $key): JsonResponse
    {
        $attribute = Attribute::find($key);

        if (!$attribute) {
            return response()->json(['message' => 'Attribute not found'], 404);
        }

        $attribute->delete();

        return response()->json(null, 204);
    }
}
