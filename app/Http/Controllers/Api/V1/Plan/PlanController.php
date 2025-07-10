<?php

namespace App\Http\Controllers\Api\V1\Plan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Application\UseCases\Plan\CreatePlanUseCase;
use App\Application\UseCases\Plan\GetPlanUseCase;
use App\Application\UseCases\Plan\ListPlansUseCase;
use App\Application\UseCases\Plan\UpdatePlanUseCase;
use App\Application\UseCases\Plan\DeletePlanUseCase;
use App\Application\UseCases\Plan\RestorePlanUseCase;
use App\Application\DTOs\Plan\CreatePlanDTO;
use App\Application\DTOs\Plan\UpdatePlanDTO;
use App\ValueObjects\Plan\PlanId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="CreatePlanRequest",
 *     required={"name", "monthly_price", "user_limit", "features"},
 *     @OA\Property(property="name", type="string", example="Basic Plan", description="Plan name"),
 *     @OA\Property(property="monthly_price", type="number", format="float", example=29.99, description="Monthly price"),
 *     @OA\Property(property="user_limit", type="integer", example=10, description="Maximum number of users"),
 *     @OA\Property(property="features", type="array", @OA\Items(type="string"), example={"feature1", "feature2"}, description="Plan features")
 * )
 * 
 * @OA\Schema(
 *     schema="UpdatePlanRequest",
 *     @OA\Property(property="name", type="string", example="Basic Plan", description="Plan name"),
 *     @OA\Property(property="monthly_price", type="number", format="float", example=29.99, description="Monthly price"),
 *     @OA\Property(property="user_limit", type="integer", example=10, description="Maximum number of users"),
 *     @OA\Property(property="features", type="array", @OA\Items(type="string"), example={"feature1", "feature2"}, description="Plan features")
 * )
 * 
 * @OA\Schema(
 *     schema="Plan",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Basic Plan"),
 *     @OA\Property(property="monthly_price", type="number", format="float", example=29.99),
 *     @OA\Property(property="user_limit", type="integer", example=10),
 *     @OA\Property(property="features", type="array", @OA\Items(type="string"), example={"feature1", "feature2"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="PlanListResponse",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Plan")),
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
 *     schema="PlanResponse",
 *     @OA\Property(property="message", type="string", example="Plan created successfully"),
 *     @OA\Property(property="data", ref="#/components/schemas/Plan")
 * )
 * 
 * @OA\Schema(
 *     schema="PlanSuccessResponse",
 *     @OA\Property(property="message", type="string", example="Plan deleted successfully")
 * )
 */
class PlanController extends Controller
{
    public function __construct(
        private CreatePlanUseCase $createPlanUseCase,
        private GetPlanUseCase $getPlanUseCase,
        private ListPlansUseCase $listPlansUseCase,
        private UpdatePlanUseCase $updatePlanUseCase,
        private DeletePlanUseCase $deletePlanUseCase,
        private RestorePlanUseCase $restorePlanUseCase
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/plans",
     *     operationId="listPlans",
     *     tags={"Plans"},
     *     summary="List all plans",
     *     description="Get paginated list of plans. Requires super-admin role.",
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
     *         description="Plans retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlanListResponse")
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

        $plans = $this->listPlansUseCase->execute($perPage, $page);

        return response()->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $plans->items()),
            'pagination' => [
                'current_page' => $plans->currentPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
                'last_page' => $plans->lastPage(),
                'from' => $plans->firstItem(),
                'to' => $plans->lastItem(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/plans",
     *     operationId="createPlan",
     *     tags={"Plans"},
     *     summary="Create a new plan",
     *     description="Create a new plan. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreatePlanRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlanResponse")
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
    public function store(CreatePlanRequest $request): JsonResponse
    {
        try {
            $dto = CreatePlanDTO::fromArray($request->validated());
            $plan = $this->createPlanUseCase->execute($dto);

            return response()->json([
                'message' => 'Plan created successfully',
                'data' => $plan->toArray()
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/plans/{id}",
     *     operationId="getPlan",
     *     tags={"Plans"},
     *     summary="Get plan by ID",
     *     description="Get a specific plan by ID. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Plan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Plan")
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
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $planId = new PlanId((int) $id);
            $plan = $this->getPlanUseCase->execute($planId);

            return response()->json([
                'data' => $plan->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/plans/{id}",
     *     operationId="updatePlan",
     *     tags={"Plans"},
     *     summary="Update plan",
     *     description="Update a specific plan. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Plan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePlanRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlanResponse")
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
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plan not found")
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
    public function update(UpdatePlanRequest $request, string $id): JsonResponse
    {
        try {
            $planId = new PlanId((int) $id);
            $dto = UpdatePlanDTO::fromArray($request->validated());
            
            if (!$dto->hasChanges()) {
                return response()->json([
                    'message' => 'No changes provided'
                ], 422);
            }

            $plan = $this->updatePlanUseCase->execute($planId, $dto);

            return response()->json([
                'message' => 'Plan updated successfully',
                'data' => $plan->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/plans/{id}",
     *     operationId="deletePlan",
     *     tags={"Plans"},
     *     summary="Delete plan",
     *     description="Soft delete a specific plan. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Plan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlanSuccessResponse")
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
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $planId = new PlanId((int) $id);
            $this->deletePlanUseCase->execute($planId);

            return response()->json([
                'message' => 'Plan deleted successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/plans/{id}/restore",
     *     operationId="restorePlan",
     *     tags={"Plans"},
     *     summary="Restore plan",
     *     description="Restore a soft deleted plan. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Plan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlanSuccessResponse")
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
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         )
     *     )
     * )
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $planId = new PlanId((int) $id);
            $this->restorePlanUseCase->execute($planId);

            return response()->json([
                'message' => 'Plan restored successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
} 