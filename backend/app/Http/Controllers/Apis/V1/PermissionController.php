<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\QueryRequest;
use App\Services\PermissionService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use ResponseHelper;

    private $permissionService;

    public function __construct(PermissionService $permissionService) {
        $this->permissionService = $permissionService;
    }

    public function paginate(QueryRequest $requets) {
        $query = $requets->validated();
        $limit = $query['limit'];

        $permissions = $this->permissionService->paginate($limit);

        return $this->successList($permissions);
    }

    public function create(PermissionRequest $request) {
        $data = $request->validated();

        $permission = $this->permissionService->create($data);

        return $this->success($permission, 'Tạo quyền thành công!', 201);
    }

    public function show($id) {
        $permission = $this->permissionService->find($id);

        return $this->success($permission);
    }

    public function update(PermissionRequest $request, $id) {
        $data = $request->validated();

        $permission = $this->permissionService->update($id, $data);

        return $this->success($permission, 'Cập nhật quyền thành công!', 200);
    } 

    public function delete($id) {
        $this->permissionService->delete($id);

        return $this->success(null, 'Xóa quyền thành công!');
    }
}
