<?php

namespace App\Http\Controllers;

use App\Core\DocumentType\CreateDocumentType;
use App\Core\DocumentType\DocumentTypeID;
use App\Core\DocumentType\UpdateDocumentType;
use App\Exceptions\NotFoundException;
use App\Services\DocumentTypeService;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DocumentTypeController extends Controller
{
    public function __construct(
        private DocumentTypeService $service
    ) { }

    public function getAll(): JsonResponse
    {
        try {
            $documentTypes = $this->service
                ->findAll()
                ->load('attributes');
            return response()->json($documentTypes);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to fetch document types'], 500);
        }
    }

    public function getById(string $id): JsonResponse
    {
        try {
            $documentType = $this->service
                ->findById(new DocumentTypeID($id))
                ->load('attributes');

            return response()->json($documentType);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document type not found'], 404);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $validated = Validator::validate($request->all(), [
                'name' => 'required|string|max:255|unique:document_types,name',
                'description' => 'nullable|string',
                'attributes' => 'sometimes|array',
                'attributes.*' => 'string|exists:attributes,key',
            ]);

            $created = $this->service
                ->create(
                    new CreateDocumentType(
                        $validated['name'],
                        $validated['description'] ?? null,
                        $validated['attributes'] ?? [],
                    )
                )
                ->load('attributes');

            return response()->json($created, 201);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = Validator::validate($request->all(), [
                'name' => 'sometimes|string|max:255|unique:document_types,name,' . $id,
                'description' => 'nullable|string',
                'attributes' => 'sometimes|array',
                'attributes.*' => 'string|exists:attributes,key',
            ]);

            $updated = $this->service
                ->update(
                    new DocumentTypeID($id),
                    new UpdateDocumentType(
                        $validated['name'],
                        $validated['description'] ?? null,
                        $validated['attributes'] ?? [],
                    )
                )
                ->load('attributes');

            return response()->json($updated);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document type not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->service->delete(new DocumentTypeID($id));
            return response()->json(null, 204);
        } catch (NotFoundException) {
            return response()->json(['message' => 'Document type not found'], 404);
        }
    }
}
