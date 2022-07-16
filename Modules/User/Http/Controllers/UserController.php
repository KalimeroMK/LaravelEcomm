<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\StoreRequest;
use Modules\User\Http\Requests\UpdateRequest;
use Modules\User\Models\User;
use Modules\User\Service\UserService;

class UserController extends Controller
{
    
    private UserService $user_service;
    
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    
    public function index(Request $request)
    {
        return view('user::index', ['users' => $this->user_service->index()])->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     *
     * @return RedirectResponse
     */
    
    public function store(StoreRequest $request): RedirectResponse
    {
        $this->user_service->store($request);
        
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }
    
    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('user::create')->with($this->user_service->$this->create());
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Application|Factory|View
     */
    
    public function show(int $id)
    {
        return view('user::edit', ['user' => $this->user_service->show($id)]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Application|Factory|View
     */
    
    public function edit(int $id)
    {
        return view('user::edit')->with($this->user_service->edit($id));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    
    public function update(UpdateRequest $request, int $id): RedirectResponse
    {
        $this->user_service->update($request, $id);
        
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     *
     * @return RedirectResponse
     */
    
    public function destroy(User $user): RedirectResponse
    {
        $this->user_service->destroy($user->id);
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
