<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\FolderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function __construct(
        private FolderService $service
    ) { }

    public function getByParent(Request $request): JsonResponse
    {
        try {
            $parentId = $request->query('parent_id');
            $parent = $parentId ? $this->service->findById($parentId) : null;
            $folders = $this->service->findByParent($parent);

            return response()->json($folders);
        } catch (NotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
