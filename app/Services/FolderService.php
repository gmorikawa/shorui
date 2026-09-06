<?php

namespace App\Services;

use App\Core\Document\DocumentID;
use App\Core\Folder\FolderID;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Folder;
use App\Models\User;

class FolderService
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    /**
     * Find folders by their parent folder.
     * 
     * Folders that do not have a parent are root level folders.
     * In that case, $parent will be null.
     *
     * @param Folder|null $parent
     * @return array
     */
    public function findByParent(?Folder $parent): array
    {
        if ($parent) {
            return Folder::where('parent_id', $parent->id)->get()->all();
        }

        return Folder::whereNull('parent_id')->get()->all();
    }

    /**
     * Find folder by its ID.
     *
     * @param FolderID $id
     * @return Folder
     * @throws NotFoundException
     */
    public function findById(FolderID $id): Folder
    {
        $folder = Folder::find($id->value);
        if (!$folder) {
            throw new NotFoundException("Folder with ID $id not found");
        }

        return $folder;
    }

    /**
     * Create a new folder.
     *
     * @param string $name
     * @param User $owner
     * @param Folder|null $parent
     * @return Folder
     */
    public function create(string $name, User $owner, ?Folder $parent): Folder {
        if ($parent && ($parent->owner->id !== $owner->id && !$owner->isAdmin())) {
            throw new ForbiddenException("Owner of the parent folder does not match the owner of the new folder");
        }

        $existingFolder = Folder::query()
            ->where('name', $name)
            ->where('parent_id', $parent?->id ?? null)
            ->first();

        if ($existingFolder) {
            throw new DuplicateException("Folder with the same name already exists under the same parent");
        }

        $folder = new Folder();
        $folder->name = $name;
        $folder->owner()->associate($owner);
        $folder->parent()->associate($parent);
        $folder->save();

        return $folder;
    }

    /**
     * Delete a folder by its ID, including all its documents and subfolders.
     *
     * @param FolderID $id
     * @throws NotFoundException
     */
    public function delete(FolderID $id): void
    {
        $folder = $this->findById($id);

        $documents = $folder->documents()->get();
        foreach ($documents as $document) {
            $this->documentService->delete(new DocumentID($document->id));
        }

        $subfolders = $folder->subfolders()->get();
        foreach ($subfolders as $subfolder) {
            $this->delete(new FolderID($subfolder->id));
        }

        $folder->delete();
    }
}
