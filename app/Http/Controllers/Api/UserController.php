<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Admission;
use App\Models\Filiale;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'admin')->orWhere('name', 'global');
        })->get();        
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        if (auth()->user()->roles->first()->name !== "admin") {
            abort(403, 'Unauthorized');
        }
        
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        
        $user_data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ];

        $user_role = $data['role'];
        $user_filiale = $data['filiale'];
        $user_admission = $data['admission'];



        $role = Role::where('name', $user_role)->firstOrFail();
        $filiale = Filiale::findOrFail($user_filiale);
        $admission = Admission::findOrFail($user_admission);

        $user = User::create($user_data);
        $user->roles()->attach($role);
        $user->filiales()->attach($filiale);
        $user->admissions()->attach($admission);

        return response(new UserResource($user) , 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateUserRequest $request
     * @param \App\Models\User                     $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        if (auth()->user()->roles->first()->name !== "admin") {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (auth()->user()->roles->first()->name !== "admin") {
            abort(403, 'Unauthorized');
        }

        $user->delete();
        return response("", 204);
    }


    

}

