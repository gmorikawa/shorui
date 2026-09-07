<?php

namespace App\Http\Controllers;

use App\Core\Folder\FolderID;
use App\Exceptions\DuplicateException;
use App\Exceptions\NotFoundException;
use App\Services\FolderService;

use Error;
use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function __construct(
        private FolderService $service
    ) {}

    public function getByParent(Request $request): JsonResponse
    {
        try {
            $parentId = $request->query('parent_id');
            $parent = $parentId ? $this->service->findById(new FolderID($parentId)) : null;
            $folders = $this->service->findByParent($parent);

            return response()->json($folders);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        } catch (Error $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function getById(string $id): JsonResponse
    {
        try {
            $folder = $this->service->findById(new FolderID($id));
            return response()->json($folder);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        } catch (Error $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $name = $request->input('name');
            $parentId = $request->input('parent_id');
            $user = $request->user();

            $parent = $parentId ? $this->service->findById(new FolderID($parentId)) : null;
            $folder = $this->service->create($name, $user, $parent);

            return response()->json($folder, 201);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (DuplicateException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        } catch (Error $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->service->delete(new FolderID($id));
            return response()->json([], 204);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        } catch (Error $error) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
