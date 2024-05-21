<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;
use App\Services\JwtService;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    private $jwtService;
    private $user;

    public function __construct()
    {
        $this->jwtService = new JwtService();
        $this->user = new User();
    }

    public function store()
    {
        $data = $this->request->getJSON();

        try {
            $userFound = $this->user->where('email', $data->email)->first();

            if (!$userFound) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['message' => 'User not found']);
            }

            if (!password_verify($data->password, $userFound->password)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['message' => 'Invalid password']);
            }

            $token = $this->jwtService->generateToken(['email' => $userFound->email]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['token' => $token]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
