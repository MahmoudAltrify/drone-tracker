<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiBaseController
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse(['Invalid credentials']);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse(['token' => $token]);
    }

    public function me(Request $request)
    {
        return $this->successResponse($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(code: Response::HTTP_NO_CONTENT);
    }
}
