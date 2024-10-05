<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

/**
 * @OA\Info(
 *     title="Events Booker API",
 *     version="1.0.0",
 *     description="API for booking and managing events",
 *     @OA\Contact(
 *         email="support@eventsbooker.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://http://localhost:8000/api",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login user",
     *     description="Login an existing user and get an access token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="customer@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('roles');

        $token = $user->createToken('eventsBooker')->plainTextToken;

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 201);
    }
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register user",
     *     description="Register a new user and get an access token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="customer"),
     *             @OA\Property(property="email", type="string", format="email", example="customer@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456789"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('customer');
        $user->load('roles');

        $token = $user->createToken('eventsBooker')->plainTextToken;

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 201);
    }
    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Get authenticated user",
     *     description="Get details of the authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="customer"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getUser(Request $request)
    {
        $user = $request->user()->load('roles');

        return response()->json(
            $user,
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     description="Revoke access token for the authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
