<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CreateCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Company\ChangePlanRequest;
use App\Application\UseCases\Company\CreateCompanyUseCase;
use App\Application\UseCases\Company\UpdateCompanyUseCase;
use App\Application\UseCases\Company\DeleteCompanyUseCase;
use App\Application\UseCases\Company\GetCompanyUseCase;
use App\Application\UseCases\Company\ListCompaniesUseCase;
use App\Application\UseCases\Company\ChangePlanUseCase;
use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Application\DTOs\Company\ChangePlanDTO;
use App\ValueObjects\Company\CompanyId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="CreateCompanyRequest",
 *     required={"name", "email", "phone", "address", "plan_id", "admin_email", "admin_password"},
 *     @OA\Property(property="name", type="string", example="Acme Corporation", description="Company name"),
 *     @OA\Property(property="email", type="string", format="email", example="contact@acme.com", description="Company email"),
 *     @OA\Property(property="phone", type="string", example="+1234567890", description="Company phone number"),
 *     @OA\Property(property="address", type="string", example="123 Main St, City, State 12345", description="Company address"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Company status"),
 *     @OA\Property(property="plan_id", type="integer", example=1, description="Plan ID"),
 *     @OA\Property(property="admin_email", type="string", format="email", example="admin@acme.com", description="Admin user email"),
 *     @OA\Property(property="admin_password", type="string", format="password", example="password123", description="Admin user password"),
 *     @OA\Property(property="admin_name", type="string", example="John Admin", description="Admin user name (optional)")
 * )
 * 
 * @OA\Schema(
 *     schema="UpdateCompanyRequest",
 *     @OA\Property(property="name", type="string", example="Acme Corporation", description="Company name"),
 *     @OA\Property(property="email", type="string", format="email", example="contact@acme.com", description="Company email"),
 *     @OA\Property(property="phone", type="string", example="+1234567890", description="Company phone number"),
 *     @OA\Property(property="address", type="string", example="123 Main St, City, State 12345", description="Company address"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Company status")
 * )
 * 
 * @OA\Schema(
 *     schema="ChangePlanRequest",
 *     required={"plan_id"},
 *     @OA\Property(property="plan_id", type="integer", example=2, description="New plan ID")
 * )
 * 
 * @OA\Schema(
 *     schema="Company",
 *     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="name", type="string", example="Acme Corporation"),
 *     @OA\Property(property="email", type="string", example="contact@acme.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Main St, City, State 12345"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="AdminUser",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Admin"),
 *     @OA\Property(property="email", type="string", example="admin@acme.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="company_id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="CompanyListResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Company")),
 *     @OA\Property(
 *         property="pagination",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=100),
 *         @OA\Property(property="last_page", type="integer", example=7)
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="CompanyResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", ref="#/components/schemas/Company"),
 *     @OA\Property(property="message", type="string", example="Empresa creada exitosamente")
 * )
 * 
 * @OA\Schema(
 *     schema="CompanyCreateResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="company", ref="#/components/schemas/Company"),
 *         @OA\Property(property="admin_user", ref="#/components/schemas/AdminUser")
 *     ),
 *     @OA\Property(property="message", type="string", example="Empresa creada exitosamente")
 * )
 * 
 * @OA\Schema(
 *     schema="Subscription",
 *     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440001"),
 *     @OA\Property(property="company_id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="plan_id", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="start_date", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="end_date", type="string", format="date-time", example="2023-12-31T23:59:59Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ChangePlanResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", ref="#/components/schemas/Subscription"),
 *     @OA\Property(property="message", type="string", example="Plan cambiado exitosamente")
 * )
 */
class CompanyController extends Controller
{
    public function __construct(
        private CreateCompanyUseCase $createCompanyUseCase,
        private UpdateCompanyUseCase $updateCompanyUseCase,
        private DeleteCompanyUseCase $deleteCompanyUseCase,
        private GetCompanyUseCase $getCompanyUseCase,
        private ListCompaniesUseCase $listCompaniesUseCase,
        private ChangePlanUseCase $changePlanUseCase
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/companies",
     *     operationId="listCompanies",
     *     tags={"Companies"},
     *     summary="List all companies",
     *     description="Get paginated list of companies. Requires super-admin role.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Companies retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyListResponse")
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
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving companies")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $result = $this->listCompaniesUseCase->execute($perPage);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'pagination' => [
                    'current_page' => $result->currentPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                    'last_page' => $result->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/companies",
     *     operationId="createCompany",
     *     tags={"Companies"},
     *     summary="Create a new company",
     *     description="Create a new company with admin user. Disponible sin autenticación.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateCompanyRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyCreateResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid data provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al crear la empresa")
     *         )
     *     )
     * )
     */
    public function store(CreateCompanyRequest $request): JsonResponse
    {
        try {
            $dto = CreateCompanyDTO::fromArray($request->validated());
            $result = $this->createCompanyUseCase->execute($dto);
            $company = $result['company'];
            $adminUser = $result['admin_user'];

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => $company,
                    'admin_user' => $adminUser,
                ],
                'message' => 'Empresa creada exitosamente'
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la empresa'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/companies/{id}",
     *     operationId="getCompany",
     *     tags={"Companies"},
     *     summary="Get company by ID",
     *     description="Get a specific company by ID. Available to all authenticated users.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
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
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al obtener la empresa")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $companyId = new CompanyId($id);
            $company = $this->getCompanyUseCase->execute($companyId);

            return response()->json([
                'success' => true,
                'data' => $company
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la empresa'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/companies/{id}",
     *     operationId="updateCompany",
     *     tags={"Companies"},
     *     summary="Update company",
     *     description="Update a specific company. Available to all authenticated users.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCompanyRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al actualizar la empresa")
     *         )
     *     )
     * )
     */
    public function update(UpdateCompanyRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['id'] = $id;
            
            $dto = UpdateCompanyDTO::fromArray($data);
            $company = $this->updateCompanyUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'data' => $company,
                'message' => 'Empresa actualizada exitosamente'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la empresa'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/companies/{id}",
     *     operationId="deleteCompany",
     *     tags={"Companies"},
     *     summary="Delete company",
     *     description="Delete a specific company. Available to all authenticated users.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empresa eliminada exitosamente")
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
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al eliminar la empresa")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $companyId = new CompanyId($id);
            $this->deleteCompanyUseCase->execute($companyId);

            return response()->json([
                'success' => true,
                'message' => 'Empresa eliminada exitosamente'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la empresa'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/companies/{id}/change-plan",
     *     operationId="changeCompanyPlan",
     *     tags={"Companies"},
     *     summary="Change company plan",
     *     description="Change the subscription plan for a company. Available to all authenticated users.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChangePlanRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan changed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ChangePlanResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid plan ID")
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al cambiar el plan")
     *         )
     *     )
     * )
     */
    public function changePlan(ChangePlanRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['company_id'] = $id;
            
            $dto = ChangePlanDTO::fromArray($data);
            $subscription = $this->changePlanUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'Plan cambiado exitosamente'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el plan'
            ], 500);
        }
    }
} 