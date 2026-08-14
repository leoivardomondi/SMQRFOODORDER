<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Services\OrderService;
use App\Exports\AdministratorExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Services\AdministratorService;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Resources\UserOrderResource;
use App\Http\Requests\AdministratorRequest;
use App\Http\Resources\AdministratorResource;
use App\Http\Requests\UserChangePasswordRequest;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Mail\AccountSetupInvitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AdministratorController extends AdminController
{
    private AdministratorService $administratorService;
    private OrderService $orderService;


    public function __construct(AdministratorService $administratorService, OrderService $orderService)
    {
        parent::__construct();
        $this->administratorService = $administratorService;
        $this->orderService   = $orderService;
        $this->middleware(['permission:administrators'])->only('index', 'export', 'changePassword', 'changeImage', 'myOrder');
        $this->middleware(['permission:administrators_create'])->only('store');
        $this->middleware(['permission:administrators_edit'])->only('update');
        $this->middleware(['permission:administrators_edit|employees_edit|delivery-boys_edit|waiters_edit|chefs_edit|customers_edit'])->only('sendSetupInvitations');
        $this->middleware(['permission:administrators_delete'])->only('destroy');
        $this->middleware(['permission:administrators_show'])->only('show');
    }

    public function sendSetupInvitations(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'distinct'],
        ]);

        $admins = User::withoutGlobalScopes()
            ->whereIn('id', $validated['user_ids'])
            ->where('status', Status::ACTIVE)
            ->whereNotNull('email')->where('email', '!=', '')->get();
        $sent = 0;
        $failed = [];

        foreach ($admins as $admin) {
            try {
                $token = Str::random(64);
                DB::table('password_resets')->where('email', $admin->email)->delete();
                DB::table('password_resets')->insert([
                    'email' => $admin->email,
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]);
                $url = URL::temporarySignedRoute('admin.setup-account', now()->addHours(24), [
                    'email' => $admin->email,
                    'token' => $token,
                ]);
                Mail::to($admin->email)->send(new AccountSetupInvitation($admin->name, $url));
                $sent++;
            } catch (\Throwable $exception) {
                $failed[] = $admin->email;
            }
        }

        return response()->json([
            'message' => $sent . ' account setup email(s) sent.',
            'sent' => $sent,
            'failed' => $failed,
        ], empty($failed) ? 200 : 207);
    }

    public function index(PaginateRequest $request) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AdministratorResource::collection($this->administratorService->list($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(AdministratorRequest $request) : AdministratorResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdministratorResource($this->administratorService->store($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(AdministratorRequest $request, User $administrator) : AdministratorResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdministratorResource($this->administratorService->update($request, $administrator));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $administrator) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->administratorService->destroy($administrator);
            return response('', 202);
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $administrator) : AdministratorResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdministratorResource($this->administratorService->show($administrator));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function export(PaginateRequest $request) : \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new AdministratorExport($this->administratorService, $request), 'Administrator.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changePassword(UserChangePasswordRequest $request, User $administrator) : AdministratorResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdministratorResource($this->administratorService->changePassword($request, $administrator));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, User $administrator) : AdministratorResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdministratorResource($this->administratorService->changeImage($request, $administrator));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function myOrder(PaginateRequest $request, User $administrator) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return UserOrderResource::collection($this->orderService->userOrder($request, $administrator));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
