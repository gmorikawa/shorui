<?php

namespace App\Http\Controllers;

use App\Enums\FileState;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class FileController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(File::all());
    }

    public function getById(string $id): JsonResponse
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->json($file);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path'  => 'required|string|unique:files,path',
            'state' => ['sometimes', new Enum(FileState::class)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = File::create($validator->validated());

        return response()->json($file, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'path'  => 'sometimes|string|unique:files,path,' . $id,
            'state' => ['sometimes', new Enum(FileState::class)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file->update($validator->validated());

        return response()->json($file);
    }

    public function delete(string $id): JsonResponse
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $file->delete();

        return response()->json(null, 204);
    }
}