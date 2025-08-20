<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laminas\Diactoros\Response as Psr7Response;
use Laravel\Passport\Client;

class AuthController extends Controller
{

    public function login(LoginRequest $request, AccessTokenController $accessTokenController)
    {
        try {
            if (!$auth = Auth::attempt($request->only('email', 'password'))) {
                return response()->json(["status" => false, "message" => "Wrong credentials..!"], Response::HTTP_UNAUTHORIZED);
            }
            $user = Auth::user();

            $tokenResult = $user->createToken('LARAVEL BILL', ['*']);
            $accessToken = $tokenResult->accessToken;
            $token = $tokenResult->token;

            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "user" => $user,
                "access_token" => $accessToken,
                "token_type" => "Bearer",
                "expires_at" => $token->expires_at,
                "refresh_token" => $token->id, // you can implement refresh manually
            ], Response::HTTP_OK);

            // $token = $user->createToken('My Token')->accessToken;

            // Call Passport's /oauth/token endpoint
            $response = Http::asForm()->post(config('services.passport.login_endpoint'), [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '*',
            ]);

            if ($response->failed()) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid credentials",
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Returns access_token + refresh_token
            return $response->json();
            if($response->failed()) {
                return response()->json(["status" => false, "message" => "Unable to create token"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json(["status" => true, "message" => "Login Success"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "error" => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
