<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\ListUsersUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Application\UseCases\User\DeleteUserUseCase;
use App\Application\UseCases\User\RestoreUserUseCase;
use App\Application\DTOs\User\CreateUserDTO;
use App\Application\DTOs\User\UpdateUserDTO;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="CreateUserRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe", description="User full name"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User email address"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", description="User password"),
 *     @OA\Property(property="company_id", type="integer", example=1, description="Company ID (optional for admin users)")
 * )
 * 
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     @OA\Property(property="name", type="string", example="John Doe", description="User full name"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User email address"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", description="User password"),
 *     @OA\Property(property="company_id", type="integer", example=1, description="Company ID")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="user@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="company_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="UserListResponse",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 *     @OA\Property(
 *         property="pagination",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=100),
 *         @OA\Property(property="last_page", type="integer", example=7),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="to", type="integer", example=15)
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="UserResponse",
 *     @OA\Property(property="message", type="string", example="User created successfully"),
 *     @OA\Property(property="data", ref="#/components/schemas/User")
 * )
 * 
 * @OA\Schema(
 *     schema="UserSuccessResponse",
 *     @OA\Property(property="message", type="string", example="User deleted successfully")
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private CreateUserUseCase $createUserUseCase,
        private GetUserUseCase $getUserUseCase,
        private ListUsersUseCase $listUsersUseCase,
        private UpdateUserUseCase $updateUserUseCase,
        private DeleteUserUseCase $deleteUserUseCase,
        private RestoreUserUseCase $restoreUserUseCase
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     operationId="listUsers",
     *     tags={"Users"},
     *     summary="List all users",
     *     description="Get paginated list of users. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserListResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $users = $this->listUsersUseCase->execute($perPage, $page);

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     operationId="createUser",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Create a new user. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Si el usuario es admin, asignar automáticamente su company_id
            if (Auth::user()->hasRole('admin')) {
                $data['company_id'] = Auth::user()->company_id;
            }
            
            $dto = CreateUserDTO::fromArray($data);
            $user = $this->createUserUseCase->execute($dto);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user->toArray()
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     operationId="getUser",
     *     tags={"Users"},
     *     summary="Get user by ID",
     *     description="Get a specific user by ID. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $userId = new UserId((int) $id);
            $user = $this->getUserUseCase->execute($userId);

            return response()->json([
                'data' => $user->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Update user",
     *     description="Update a specific user. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No changes provided")
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $userId = new UserId((int) $id);
            $data = $request->validated();
            
            // Si el usuario es admin, solo puede actualizar su propia company_id
            if (Auth::user()->hasRole('admin')) {
                $data['company_id'] = Auth::user()->company_id;
            }
            
            $dto = UpdateUserDTO::fromArray($data);
            
            if (!$dto->hasChanges()) {
                return response()->json([
                    'message' => 'No changes provided'
                ], 422);
            }

            $user = $this->updateUserUseCase->execute($userId, $dto);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     summary="Delete user",
     *     description="Soft delete a specific user. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $userId = new UserId((int) $id);
            $this->deleteUserUseCase->execute($userId);

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/users/{id}/restore",
     *     operationId="restoreUser",
     *     tags={"Users"},
     *     summary="Restore user",
     *     description="Restore a soft deleted user. Requires super-admin or admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Insufficient permissions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $userId = new UserId((int) $id);
            $this->restoreUserUseCase->execute($userId);

            return response()->json([
                'message' => 'User restored successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
} 