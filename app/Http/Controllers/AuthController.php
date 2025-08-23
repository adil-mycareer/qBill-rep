<?php

namespace App\Http\Controllers;

use App\APIResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laminas\Diactoros\Response as Psr7Response;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    use APIResponse;

    public function login(LoginRequest $request, AccessTokenController $accessTokenController)
    {
        if (!$auth = Auth::attempt($request->only('email', 'password'))) {
            return response()->json(["status" => false, "message" => "Wrong credentials..!"], Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::user();

        $tokenResult = $user->createToken('LARAVEL BILL', ['*']);
        $accessToken = $tokenResul->accessToken;
        $token = $tokenResult->token;

        return $this->successResponse(
            [
                "user" => $user,
                "access_token" => $accessToken,
                "token_type" => "Bearer",
                "expires_at" => $token->expires_at,
                "refresh_token" => $token->id,
            ],
            "Login successfull",
        );
    }
}
