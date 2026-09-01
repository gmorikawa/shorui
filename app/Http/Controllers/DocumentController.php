<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $service
    ) { }

    public function getAll(): JsonResponse
    {
        return response()->json($this->service->findAll());
    }

    public function getByFolder(string $folderId): JsonResponse
    {
        return response()->json($this->service->findByFolder($folderId));
    }

    public function getById(string $id): JsonResponse
    {
        try {
            return response()->json($this->service->findById($id));
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json($this->service->create($request->all(), $request->user()), 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            return response()->json($this->service->update($id, $request->all()));
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(null, 204);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        $file = $this->service->upload($request->file('file'));

        return response()->json(['message' => 'File uploaded successfully', 'data' => $file], 201);
    }
}