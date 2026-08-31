<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['auth:sanctum']);
    }

    public function index(Request $request)
    {
        try {
            $query = User::query();

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->get('paginate', 0) == 1) {
                $users = $query->paginate($request->get('per_page', 10));
                return UserResource::collection($users);
            } else {
                $users = $query->orderBy('name', 'asc')->limit(500)->get();
                return UserResource::collection($users);
            }
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
