<?php

namespace App\Http\Controllers;

use App\Enums\FileState;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadedFile = $request->file('file');
        $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs('', $filename, 'local');

        $file = File::create([
            'path'  => $path,
            'state' => FileState::AVAILABLE,
        ]);

        return response()->json($file, 201);
    }

    public function download(string $id): JsonResponse|StreamedResponse
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        if (!Storage::disk('local')->exists($file->path)) {
            return response()->json(['message' => 'File content not found'], 404);
        }

        return Storage::disk('local')->download($file->path);
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