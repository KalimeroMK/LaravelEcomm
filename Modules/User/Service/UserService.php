<?php

namespace Modules\User\Service;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;
use Spatie\Permission\Models\Role;

class UserService
{
    private UserRepository $user_repository;
    
    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $currentUser = auth()->user();

        if ($currentUser->isSuperAdmin()) {
         return $this->user_repository->findAll();
        } else {
            return collect([$this->user_repository->findById($currentUser->id)]);
        }
    }


    /**
     * @param $request
     *
     * @return void
     */
    public function register($request): void
    {
        $input             = $request->all();
        $user              = User::create($input);
        $user->assignRole($request->input('roles'));
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return void
     */
    public function update($id, $data): void
    {
        $input = $data;
        
        if ( ! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }
        
        $user = User::findOrFail($id);
        
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($data->input('roles'));
    }
    
    /**
     * @param $id
     *
     * @return array
     */
    public function edit($id): array
    {
        return [
            'user'     => User::find($id),
            'roles'    => Role::all(),
            'userRole' => User::find($id)->roles->pluck('name', 'name')->all(),
        ];
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        return $this->user_repository->findById($id);
    }
    
    public function create(): array
    {
        return [
            'user'  => new User(),
            'roles' => Role::all(),
        ];
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->user_repository->delete($id);
    }
    
    /**
     * @param $id
     *
     * @return LengthAwarePaginator|_IH_Order_C|Order[]
     */
    public function orderUser($id)
    {
        return Order::with('shipping', 'user')->orderBy('id', 'DESC')->whereUserId($id)->paginate(10);
    }
    
    /**
     * @param $data
     *
     * @return void
     */
    public function store($data): void
    {
        $this->user_repository->create($data);
    }
    
}