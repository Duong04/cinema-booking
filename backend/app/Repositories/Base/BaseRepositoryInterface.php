<?php 
namespace App\Repositories\Base;

interface BaseRepositoryInterface
{
    public function all(int $limit = 15, array $with = []);
    public function find(string $id, array $columns = ['*'], array $with = []);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}