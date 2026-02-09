<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()->orderBy('name')->get();

        return UserResource::collection($users);
    }
}
