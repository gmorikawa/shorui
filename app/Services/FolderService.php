<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Folder;
use App\Models\User;

class FolderService
{
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
     * @param string $id
     * @return Folder
     * @throws NotFoundException
     */
    public function findById(string $id): Folder
    {
        $folder = Folder::find($id);
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
        $folder = new Folder();
        $folder->name = $name;
        $folder->owner()->associate($owner);
        $folder->parent()->associate($parent);
        $folder->save();

        return $folder;
    }
}
