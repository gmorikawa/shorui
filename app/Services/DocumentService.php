<?php

namespace App\Services;

use App\Enums\FileState;
use App\Exceptions\NotFoundException;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use App\Core\Document\DocumentID;
use App\Core\File\FileID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function __construct(
        private readonly FileService $fileService
    ) { }

    public function findAll(): Collection
    {
        return Document::all()->load('type', 'file', 'user');
    }

    public function findByFolder(string $folderId): Collection
    {
        return Document::where('folder_id', $folderId)->get()->load('type', 'file', 'user');
    }

    public function findById(DocumentID $id): Document
    {
        $document = Document::find($id->value);

        if (!$document) {
            throw new NotFoundException("Document with ID $id->value not found");
        }

        return $document->load('type', 'file', 'user');
    }

    /**
     * @param array $data
     * @param User $user
     * @return Document
     * @throws ValidationException
     */
    public function create(array $data, User $user): Document
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'type_id' => 'required|uuid|exists:document_types,id',
            'attributes' => 'sometimes|array',
            'folder_id' => 'required|uuid|exists:folders,id',
            'file_id' => 'required|uuid|exists:files,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $document = $validator->validated();
        $document['user_id'] = $user->id;

        return Document::create($document);
    }

    /**
     * @param DocumentID $id
     * @param array $data
     * @return Document
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(DocumentID $id, array $data): Document
    {
        $document = $this->findById($id);

        $validator = Validator::make($data, [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type_id' => 'sometimes|uuid|exists:document_types,id',
            'attributes' => 'sometimes|array',
            'folder_id' => 'required|uuid|exists:folders,id',
            'file_id' => 'required|uuid|exists:files,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $document->update($validator->validated());

        return $document;
    }

    /**
     * @throws NotFoundException
     */
    public function delete(DocumentID $id): void
    {
        $this->findById($id)->delete();
    }

    public function upload(UploadedFile $uploadedFile): File
    {
        $path = $uploadedFile->store('uploads', 'public');

        return File::create([
            'path' => $path,
            'state' => FileState::AVAILABLE,
        ]);
    }

    public function download(DocumentID $id): StreamedResponse
    {
        $document = $this->findById($id)->load('file');

        return $this->fileService->download(new FileID($document->file->id));
    }
}
