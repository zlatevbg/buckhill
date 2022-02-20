<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\JWTToken;
use App\Http\Requests\StoreAdminRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Login admin and generate access token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, JWTToken $jwtToken)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            throw new TokenInvalidException('Unauthorized');
        }

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
     * Log the admin out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(JWTToken $jwtToken)
    {
        $user = Auth::user();
        $jwtToken::where('user_id', $user->id)->delete();
        Auth::logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Display a listing of the non-admin users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsers(Request $request)
    {
        $users = User::where('is_admin', 0)->orderBy($request->input('sortBy', 'id'), $request->input('desc') ? 'desc' : 'asc');

        $filters = [
            'first_name' => 'first_name',
            'email' => 'email',
            'phone_number' => 'phone',
            'address' => 'address',
            'created_at' => 'created_at',
            'is_marketing' => 'marketing',
        ];

        $fields = array_flip($filters);

        foreach ($request->only($filters) as $key => $value) {
            $users = $users->where($fields[$key], $value);
        }

        $users = $users->paginate($request->input('limit', 10));

        return response()->json($users);
    }

    /**
     * Register new admin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreAdminRequest $request)
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'is_admin' => 1,
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'avatar' => $request->input('avatar'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'is_marketing' => $request->input('marketing'),
        ]);

        return response()->json($user);
    }
}
