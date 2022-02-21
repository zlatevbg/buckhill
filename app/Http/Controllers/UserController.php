<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\JWTToken;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Login user and generate access token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request, JWTToken $jwtToken)
    {
        $credentials = $request->only('email', 'password') + ['is_admin' => 0];

        if (!$token = JWTAuth::attempt($credentials)) {
            throw new TokenInvalidException('Unauthorized');
        }

        /** @var User */
        $user = Auth::user();
        $user->last_login_at = Carbon::now();
        $user->save();

        $jwtToken::where('user_id', $user->id)->delete();

        $jwtToken->user_id = $user->id;
        $jwtToken->unique_id = hash('sha256', $token);
        $jwtToken->token_title = $user->name;
        $jwtToken->save();

        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(JWTToken $jwtToken)
    {
        /** @var User */
        $user = Auth::user();
        $jwtToken::where('user_id', $user->id)->delete();
        Auth::logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Display user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        /** @var User */
        $user = Auth::user();
        return response()->json(new UserResource($user));
    }

    /**
     * Delete user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        /** @var User */
        $user = Auth::user();
        $user->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listOrders(Request $request)
    {
        /** @var User */
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc')->paginate($request->input('limit', 10));
        $ordersData = $orders->toArray();
        $ordersData['data'] = OrderResource::collection($orders->items());

        return response()->json($ordersData);
    }

    /**
     * Password forgotten.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $reset_token = null;
        $status = Password::sendResetLink($request->only('email'), function ($user, $token) use (&$reset_token) {
            $reset_token = $token;
        });

        if ($reset_token) {
            return response()->json([
                'reset_token' => $reset_token,
            ]);
        }

        return response()->json([
            'error' => trans($status),
        ]);
    }

    /**
     * Reset password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordToken(ResetUserPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // $user->forceFill([
                //     'password' => Hash::make($password)
                // ])->setRememberToken(Str::random(60));

                // $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $response = [
                'success' => trans($status),
            ];
        } else {
            $response = [
                'error' => trans($status),
            ];
        }

        return response()->json($response);
    }

    /**
     * Register new user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreUserRequest $request)
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'is_admin' => 0,
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'avatar' => $request->input('avatar'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'is_marketing' => $request->input('marketing'),
        ]);

        return response()->json(new UserResource($user));
    }

    /**
     * Edit user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UpdateUserRequest $request)
    {
        /** @var User */
        $user = Auth::user();
        $user->update($request->all());

        return response()->json(new UserResource($user));
    }
}
