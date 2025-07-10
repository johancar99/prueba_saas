<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Application\UseCases\Auth\LoginUseCase;
use App\Application\UseCases\Auth\LogoutUseCase;
use App\Application\UseCases\Auth\LogoutAllTokensUseCase;
use App\Application\UseCases\Auth\RefreshTokenUseCase;
use App\Application\DTOs\Auth\LoginDTO;
use App\ValueObjects\Auth\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User email address"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", description="User password")
 * )
 * 
 * @OA\Schema(
 *     schema="AuthResponse",
 *     @OA\Property(property="message", type="string", example="Login successful"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="access_token", type="string", example="1|abc123..."),
 *         @OA\Property(property="token_type", type="string", example="Bearer"),
 *         @OA\Property(property="expires_in", type="integer", example=3600),
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="UserProfile",
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="user@example.com"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     @OA\Property(property="message", type="string", example="Invalid credentials")
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        private LoginUseCase $loginUseCase,
        private LogoutUseCase $logoutUseCase,
        private LogoutAllTokensUseCase $logoutAllTokensUseCase,
        private RefreshTokenUseCase $refreshTokenUseCase
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticate user and return access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDTO::fromArray($request->validated());
            $response = $this->loginUseCase->execute($dto);

            return response()->json([
                'message' => 'Login successful',
                'data' => $response->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     summary="User logout",
     *     description="Logout current user session",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No token provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout failed")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'message' => 'No token provided'
                ], 401);
            }

            $tokenValueObject = new Token($token);
            $this->logoutUseCase->execute($tokenValueObject);

            return response()->json([
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout-all",
     *     operationId="logoutAll",
     *     tags={"Authentication"},
     *     summary="Logout all sessions",
     *     description="Logout user from all active sessions",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="All sessions logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="All sessions logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No token provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout failed")
     *         )
     *     )
     * )
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'message' => 'No token provided'
                ], 401);
            }

            $tokenValueObject = new Token($token);
            $this->logoutAllTokensUseCase->execute($tokenValueObject);

            return response()->json([
                'message' => 'All sessions logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     summary="Refresh access token",
     *     description="Refresh the current access token",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refresh failed")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'message' => 'No token provided'
                ], 401);
            }

            $tokenValueObject = new Token($token);
            $response = $this->refreshTokenUseCase->execute($tokenValueObject);

            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => $response->toArray()
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     operationId="getProfile",
     *     tags={"Authentication"},
     *     summary="Get user profile",
     *     description="Get current authenticated user profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserProfile")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }
} 