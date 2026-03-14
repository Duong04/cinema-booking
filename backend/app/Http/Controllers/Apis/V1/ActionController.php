<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionRequest;
use App\Http\Requests\QueryRequest;
use App\Services\ActionService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    use ResponseHelper;

    private $actionService;

    public function __construct(ActionService $actionService) {
        $this->actionService = $actionService;
    }

    public function paginate(QueryRequest $requets) {
        $query = $requets->validated();
        $limit = $query['limit'];

        $actions = $this->actionService->paginate($limit);

        return $this->successList($actions);
    }

    public function create(ActionRequest $request) {
        $data = $request->validated();

        $action = $this->actionService->create($data);

        return $this->success($action, 'Tạo hành động thành công!', 201);
    }

    public function show($id) {
        $action = $this->actionService->find($id);

        return $this->success($action);
    }

    public function update(ActionRequest $request, $id) {
        $data = $request->validated();

        $action = $this->actionService->update($id, $data);

        return $this->success($action, 'Cập nhật hành động thành công!', 200);
    } 

    public function delete($id) {
        $this->actionService->delete($id);

        return $this->success(null, 'Xóa hành động thành công!');
    }
}
