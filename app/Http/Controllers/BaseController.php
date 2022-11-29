<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JsonException;

class BaseController extends Controller
{

    /**
     * return success method.
     *
     * @param $result
     * @param string $message
     * @return JsonResponse
     */
    public function sendResponse($result, string $message = ''): JsonResponse
    {
        $response = [
            'status' => true,
            'data'    => $result,
            'response' => $message,
        ];


        return response()->json($response, 200);
    }

    public function treatmentDTO(bool $status, $data): array
    {
        return [
            'status' => $status,
            'data' => $data
        ];
    }


    /**
     * return error response.
     *
     * @param $error
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, int $code = 500): JsonResponse
    {
        $response = [
            'status' => false,
            'data' => $error,
        ];

        return response()->json($response, $code);
    }

    /**
     * Control DTO
     */
    public function validateBody(Request $request, Array $body): JsonResponse|bool
    {

        $validator = Validator::make($request->all(), $body);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        return true;
    }

    /**
     * Verify Treatment Status
     */
    public function verifyTreatmentStatus($response): JsonResponse
    {
        if ($response['status']){
            return $this->sendResponse($response['data']);
        }
        return $this->sendError($response['data']);
    }

    public function pictureUrl($filename): string{
        return env('APP_URL').'picture/'.$filename;
    }

    public function sendMail(String $email, Array $content, String $service):void{
        switch ($service){
            case "confirmationMail":
                $user = User::query()->where([
                    ['email', $content['email']]
                ])->first();

                $user->password = $content['password'];

                $content['id'] = $user->id;
                $content['token'] = Str::random(64);

                $url = $this->chooseBaseUrl('back-office', $content);

                $token = new UserVerify();
                $token->createToken($content);

                Mail::to($email)->send(new VerificationEmail($user, $url));
                break;
            case "resendVerificationEmail":
                '';
                break;
        }
    }

    public function chooseBaseUrl(string $canal, array $parameters): string
    {
        return match ($canal) {
            'back-office' => $this->generateVerificationMailUrl([
                'domain' => env('BACK_OFFICE_URL'),
                'id' => $parameters['id'],
                'token' => $parameters['token'],
            ]),
            'front-office' => $this->generateVerificationMailUrl([
                'domain' => env('FRONT_OFFICE_URL'),
                'id' => $parameters['id'],
                'token' => $parameters['token'],
            ]),
            default => '',
        };
    }

    public function generateVerificationMailUrl(array $parameters): string
    {
        URL::forceScheme('https');
        return route(
            'verification-email', [
                'domain'=> $parameters['domain'],
                'id' => $parameters['id'],
                'token' =>  $parameters['token']
            ],
            true
        );
    }

    /**
     * @throws JsonException
     */
    public function convertJson($data){
        $data = utf8_encode($data);
        return json_decode($data, true,512, JSON_THROW_ON_ERROR);
    }

}
