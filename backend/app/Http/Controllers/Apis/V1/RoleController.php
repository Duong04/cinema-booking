<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Traits\ResponseHelper;

class RoleController extends Controller
{
    use ResponseHelper;

    private $roleService;

    public function __construct(RoleService $roleService) {
        $this->roleService = $roleService;
    }

    public function paginate(QueryRequest $requets) {
        $query = $requets->validated();
        $limit = $query['limit'];
        $q = $query['q'];

        $roles = $this->roleService->paginate($limit, $q);

         return $this->successList($roles, [
            'total'            => $roles->total(),
            'per_page'         => $roles->perPage(),
            'current_page'     => $roles->currentPage(),
            'last_page'        => $roles->lastPage(),
            'current_page_url' => $roles->url($roles->currentPage()),
            'first_page_url'   => $roles->url(1),
            'last_page_url'    => $roles->url($roles->lastPage()),
            'next_page_url'    => $roles->nextPageUrl(),
            'prev_page_url'    => $roles->previousPageUrl(),
        ]);
    }

    public function create(RoleRequest $request) {
        $data = $request->validated();

        $role = $this->roleService->create($data);

        return $this->success($role, 'Tạo vai trò thành công!', 201);
    }

    public function show($id) {
        $role = $this->roleService->find($id);

        return $this->success($role);
    }

    public function update(RoleRequest $request, $id) {
        $data = $request->validated();

        $role = $this->roleService->update($id, $data);

        return $this->success($role, 'Cập nhật vai trò thành công!', 200);
    } 

    public function delete($id) {
        $this->roleService->delete($id);

        return $this->success(null, 'Xóa vai trò thành công!');
    }
}
