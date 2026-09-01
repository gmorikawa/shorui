<?php

namespace App\Services;

use App\Enums\FileState;
use App\Exceptions\NotFoundException;
use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    public function findAll(): Collection
    {
        return File::all();
    }

    public function findById(string $id): File
    {
        $file = File::find($id);

        if (!$file) {
            throw new NotFoundException('File not found');
        }

        return $file;
    }

    /**
     * @throws ValidationException
     */
    public function upload(array $data): File
    {
        $validator = Validator::make($data, [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $uploadedFile = $validator->validated()['file'];
        $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs('', $filename, 'local');

        return File::create([
            'path' => $path,
            'state' => FileState::AVAILABLE,
        ]);
    }

    /**
     * @throws NotFoundException
     */
    public function download(string $id): StreamedResponse
    {
        $file = $this->findById($id);
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        if (!$disk->exists($file->path)) {
            throw new NotFoundException('File content not found');
        }

        return $disk->download($file->path);
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function update(string $id, array $data): File
    {
        $file = $this->findById($id);

        $validator = Validator::make($data, [
            'path' => 'sometimes|string|unique:files,path,' . $id,
            'state' => ['sometimes', new Enum(FileState::class)],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $file->update($validator->validated());

        return $file;
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $id): void
    {
        $this->findById($id)->delete();
    }
}