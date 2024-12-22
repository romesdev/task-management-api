<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\POST(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register user",
     *     description="Register user",
     *
     *     @OA\RequestBody(
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="name", type="string", example="john doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com", description="O e-mail deve ser único"),
     *              @OA\Property(property="password", type="string", example="123456")
     *          ),
     *      ),
     *
     *      @OA\Response(response=200, description="Login"),
     *      @OA\Response(response=422, description="Unprocessable Content, validation errors"),
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors(), 'Validation Error.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $input = $validator->validated();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] = $user->createToken('api')->plainTextToken;
            $success['name'] = $user->name;

            return $this->responseSuccess($success, 'User register successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login",
     *     description="Login",
     *
     *     @OA\RequestBody(
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="password", type="string", example="123456")
     *          ),
     *      ),
     *
     *      @OA\Response(response=200, description="Login"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors(), 'Validation Error.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $success['token'] = $request->user()->createToken('api')->plainTextToken;

                return $this->responseSuccess($success, 'User login successfully', Response::HTTP_OK);
            }

            return $this->responseError(['error' => 'Unauthorised'], 'Unauthorised.', Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     summary="Logout",
     *     description="Logout",
     *
     *     @OA\Response(response=200, description="Logout" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->responseSuccess(null, 'Logged out successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
