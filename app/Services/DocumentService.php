<?php

namespace App\Services;

use App\Core\File\FileState;
use App\Exceptions\NotFoundException;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use App\Core\Document\CreateDocument;
use App\Core\Document\DocumentID;
use App\Core\Document\UpdateDocument;
use App\Core\File\FileID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
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
     * @param CreateDocument $data
     * @param User $user
     * @return Document
     */
    public function create(CreateDocument $data, User $user): Document
    {
        return Document::create([
            'title' => $data->title,
            'description' => $data->description,
            'type_id' => $data->typeId,
            'attributes' => $data->attributes,
            'folder_id' => $data->folderId,
            'file_id' => $data->fileId,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @param DocumentID $id
     * @param UpdateDocument $data
     * @return Document
     * @throws NotFoundException
     */
    public function update(DocumentID $id, UpdateDocument $data): Document
    {
        $document = $this->findById($id);

        $document->update([
            'title' => $data->title,
            'type_id' => $data->typeId,
            'description' => $data->description,
            'attributes' => $data->attributes,
            'folder_id' => $data->folderId,
            'file_id' => $data->fileId,
        ]);

        return $document;
    }

    /**
     * @throws NotFoundException
     */
    public function delete(DocumentID $id): void
    {
        $document = $this->findById($id);
        $this->fileService->delete(new FileID($document->file->id));

        $document->delete();
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
