<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use JsonException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends AccessTokenController
{
    /**
     * @OA\Post(
     * path="/oauth/signin",
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
    public function signin(ServerRequestInterface $request): Collection
    {
        $request_body = $request->getParsedBody();
        $request = $request->withParsedBody([
            "client_id" => env('PASSPORT_CLIENT_ID'),
            "client_secret" => env('PASSPORT_CLIENT_SECRET'),
            "scope" => env('PASSPORT_SCOPE'),
            "grant_type" => env('PASSPORT_GRANT_TYPE'),
            "username" => $request_body["username"],
            "password" => $request_body["password"],

        ]);
        $tokenResponse = parent::issueToken($request);
        $token = $tokenResponse->getContent();
        $tokenInfo = json_decode($token, true);

        return collect($tokenInfo);
    }
}
