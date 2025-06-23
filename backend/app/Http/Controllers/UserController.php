<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Enums\RoleEnum;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            $data = $this->userService->index();
            return response()->json([
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lấy danh sách người dùng thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(UserCreateRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $user = $this->userService->store($validatedData);

            return response()->json([
                'message' => 'Đăng ký thành công!',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng ký thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        // Admin can view any user
        if (auth()->user()->role->name === 'ADMIN') {
            return $this->userService->show($id);
        }
        
        if (auth()->id() != $id) {
            return response()->json(['message' => 'Không có quyền truy cập!'], 403);
        }

        return $this->userService->showWithId($id);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $validatedData = $request->validated();
        if (
            auth()->user()->role->value === RoleEnum::ADMIN->value || 
            (
                auth()->user()->role->value === RoleEnum::STAFF->value && 
                $request->role === RoleEnum::CUSTOMER->value
            )
        ) 
        {
            return response()->json($this->userService->update($validatedData, $id), 200);
        }


        if (auth()->id() != $id) {
            return response()->json(['message' => 'Không có quyền cập nhật!'], 403);
        }

        return response()->json($this->userService->update($validatedData, $id), 200);
    }

    public function destroy($id)
    {
        try {
            $this->userService->destroy($id);
            return response()->json(['message' => 'Xóa người dùng thành công'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xóa người dùng thất bại',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUserOrders(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $status = $request->query('status', '');
        try {
            $user = auth()->user();
            $data = $this->userService->getRentals($user, $page, $limit, $status);
            return response()->json([
                'data' => $data,
                'message' => 'Lấy danh sách đơn hàng thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lấy danh sách đơn hàng thất bại',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }
}
