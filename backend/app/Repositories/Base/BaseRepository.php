<?php

namespace App\Repositories\Base;

use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function all(int $limit = 15, array $with = [])
    {
        return $this->model->with($with)->orderByDesc('created_at')->paginate($limit);
    }

    public function find(string $id, array $columns = ['*'], array $with = [])
    {
        return $this->model->with($with)->findOrFail($id, $columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $model = $this->model->findOrFail($id);

        $model->update($data);

        return $model;
    }

    public function delete(string $id)
    {
        return $this->find($id)->delete();
    }
}