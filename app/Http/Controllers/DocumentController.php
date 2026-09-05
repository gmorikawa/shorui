<?php

namespace App\Http\Controllers;

use App\Core\Document\DocumentID;
use App\Exceptions\NotFoundException;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            $document = $this->service->findById(new DocumentID($id));

            return response()->json($document);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $document = $this->service->create($request->all(), $request->user());
            return response()->json($document, 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $this->service->update(new DocumentID($id), $request->all());
            return response()->json();
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function delete(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->delete(new DocumentID($id));
            DB::commit();
            return response()->json(null, 204);
        } catch (NotFoundException) {
            DB::rollBack();
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        $file = $this->service->upload($request->file('file'));

        return response()->json(['message' => 'File uploaded successfully', 'data' => $file], 201);
    }

    public function download(string $id): StreamedResponse|JsonResponse
    {
        try {
            return $this->service->download(new DocumentID($id));
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }
}
