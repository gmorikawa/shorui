<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function __construct(
        private FileService $service
    ) { }

    public function getAll(): JsonResponse
    {
        return response()->json($this->service->findAll());
    }

    public function getById(string $id): JsonResponse
    {
        try {
            return response()->json($this->service->findById($id));
        } catch (NotFoundException) {
            return response()->json(['message' => 'File not found'], 404);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        try {
            return response()->json($this->service->upload($request->all()), 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function download(string $id): JsonResponse|StreamedResponse
    {
        try {
            return $this->service->download($id);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            return response()->json($this->service->update($id, $request->all()));
        } catch (NotFoundException) {
            return response()->json(['message' => 'File not found'], 404);
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
            return response()->json(['message' => 'File not found'], 404);
        }
    }
}