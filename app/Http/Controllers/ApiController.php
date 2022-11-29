<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends BaseController
{

    protected function getBearerToken(Request $request): string
    {
        return "Bearer ".$request->bearerToken();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    protected function authenticate(){
        $client = new Client(['base_uri' => env('API_URL')]);
        $auth = $client->request('post', 'auth/admin',[
            'form_params' => [
                'email' => $this->username,
                'password' => $this->password
            ]
        ]);
        return $this->convertJson($auth->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */

    public function requestData(string $method='', string $uri=''){
        $client = new Client();
        $res = $client->request($method, env('API_URL').'/v1/'.$uri, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->InitAPIs(),
                'Accept' => 'application/json',
            ]
        ]);
        return $this->convertJson($res->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */

    public function sendData($method, $uri, $payload){
        $client = new Client();
        $res = $client->request($method, env('API_URL').'/v1'.$uri, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->InitAPIs(),
                'Accept' => 'application/json',
            ],
            'form_params' => $payload
        ]);
        return $this->convertJson($res->getBody()->getContents());
    }

    public function setMessage(string $successMessage, string $failMessage, $status): string
    {
        $session = $failMessage;
        if($status === 200) {
            $session = $successMessage;
        }
        return $session;
    }
}
