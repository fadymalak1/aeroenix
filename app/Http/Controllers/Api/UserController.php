<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->latest();

        $search = $request->query('search');
        if ($search) {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], (string) $search).'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }

        $role = UserRole::tryFrom((string) $request->query('role', ''));
        if ($role) {
            $query->where('role', $role);
        }

        $status = UserStatus::tryFrom((string) $request->query('status', ''));
        if ($status) {
            $query->where('status', $status);
        }

        $paginator = $this->paginate($query, $request);

        return $this->paginateResponse($paginator, UserResource::class, $request);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::from($data['role']),
            'status' => UserStatus::from($data['status']),
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        if (array_key_exists('name', $data)) {
            $user->name = $data['name'];
        }
        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        if (array_key_exists('role', $data)) {
            $user->role = UserRole::from($data['role']);
        }
        if (array_key_exists('status', $data)) {
            $user->status = UserStatus::from($data['status']);
        }

        $user->save();

        return new UserResource($user->fresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === $request->user()->id) {
            abort(422, 'You cannot delete your own account.');
        }

        $user->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
