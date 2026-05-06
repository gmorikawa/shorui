<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentTypeController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(DocumentType::all());
    }

    public function getById(string $id): JsonResponse
    {
        $type = DocumentType::find($id);

        if (!$type) {
            return response()->json(['message' => 'Document type not found'], 404);
        }

        return response()->json($type);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:document_types,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = DocumentType::create($validator->validated());

        return response()->json($type, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $type = DocumentType::find($id);

        if (!$type) {
            return response()->json(['message' => 'Document type not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255|unique:document_types,name,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type->update($validator->validated());

        return response()->json($type);
    }

    public function delete(string $id): JsonResponse
    {
        $type = DocumentType::find($id);

        if (!$type) {
            return response()->json(['message' => 'Document type not found'], 404);
        }

        $type->delete();

        return response()->json(null, 204);
    }
}