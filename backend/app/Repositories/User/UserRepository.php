<?php 
namespace App\Repositories\User;

use App\Repositories\Base\BaseRepository;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function paginate($limit =  15, $q, $roleId, $ignoreRoleId, $isActive) {
        $users = $this->model->with(['role:id,name', 'membership:id,user_id,tier,points'])
            ->withCount(['confirmedBookingItems as tickets_purchased_count'])
            ->when($q, fn ($query) => $query->where('name', 'like', "%$q%"))
            ->when($roleId, fn ($query) => $query->where('role_id', $roleId))
            ->when($ignoreRoleId, fn ($query) => $query->where('role_id', '!=', $ignoreRoleId))
            ->when($isActive !== null, fn ($query) => $query->where('is_active', $isActive));

            return $users->orderByDesc('created_at')->paginate($limit);
    }

    public function findWithMembershipStats(string $id)
    {
        return $this->model
            ->with(['role:id,name,description', 'membership:id,user_id,tier,points'])
            ->withCount(['confirmedBookingItems as tickets_purchased_count'])
            ->findOrFail($id);
    }

    public function getByToken(string $token, array $columns = ['*']) {
        return $this->model->where('email_verify_token', $token)
                ->first($columns);
    }

    public function getByEmail(string $email, array $columns = ['*']) {
        return $this->model->where('email', $email)
                ->first($columns);
    }
}
