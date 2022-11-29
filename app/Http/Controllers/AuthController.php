<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use JsonException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     * path="/auth/signin",
     * operationId="signin",
     * tags={"Auth"},
     * summary="Authentification",
     * description="OAuthLogin",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *          @OA\Property(property="username"),
     *          @OA\Property(property="password"),
     *     ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Connexion Réussie",
     *       ),
     * )
     */
    public function passportUserSignin(Request $request): JsonResponse|bool
    {
        try {
            $dto = $this->validateBody(
                $request,[
                    'username' => 'required',
                    'password' => 'required'
                ]
            );
            if ($dto !== true){
                return $dto;
            }
            $username = $request->input('username');
            $password = $request->input('password');

            $auth = Http::post(env('APP_URL')."/oauth/token", [
                'client_id' => env('PASSPORT_CLIENT_ID'),
                'grant_type' => 'password',
                'client_secret' => env('PASSPORT_CLIENT_SECRET'),
                'scope' => '',
                'username' => $username,
                'password' => $password
            ]);
            $conversion = $this->convertJson($auth->body());
            if ($auth->failed()){
                $response = $this->sendError($conversion);
            }else{
                $response = $this->sendResponse($conversion,'success');
            }
            return $response;
        }catch (HttpException $e){
            return $this->sendError($e);
        }
    }
}
