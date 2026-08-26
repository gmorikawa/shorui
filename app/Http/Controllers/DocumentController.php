<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(Document::all()->load('type', 'file', 'user'));
    }

    public function getById(string $id): JsonResponse
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        return response()->json($document->load('type', 'file', 'user'));
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'description' => 'sometimes|string',
            'type_id' => 'required|uuid|exists:document_types,id',
            'attributes' => 'sometimes|array',
            'file_id' => 'required|uuid|exists:files,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document = $validator->validated();
        $document['user_id'] = $request->user()->id;

        $document = Document::create($document);

        return response()->json($document, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'   => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type_id' => 'sometimes|uuid|exists:document_types,id',
            'attributes' => 'sometimes|array',
            'file_id' => 'sometimes|uuid|exists:files,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document->update($validator->validated());

        return response()->json($document);
    }

    public function delete(string $id): JsonResponse
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $document->delete();

        return response()->json(null, 204);
    }

    public function upload(Request $request, string $id): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $file = new File([
            'path' => $path,
            'state' => \App\Enums\FileState::AVAILABLE
        ]);

        $file->save();

        return response()->json(['message' => 'File uploaded successfully', 'data' => $file], 201);
    }
}