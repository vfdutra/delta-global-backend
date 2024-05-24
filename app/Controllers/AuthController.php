<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

use App\Models\User;
use App\Services\JwtService;

class AuthController extends BaseController
{
    use ResponseTrait;

    private $jwtService;
    private $user;
    private $validation;

    public function __construct()
    {
        $this->jwtService = new JwtService();
        $this->user = new User();
        $this->validation = \Config\Services::validation();
    }

    public function store()
    {
        $data = $this->request->getJSON();

        try {
            $userFound = $this->user->where('email', $data->email)->first();

            if (!$userFound) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => ['email' => 'User not registered']]);
            }

            if (!password_verify($data->password, $userFound->password)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['message' => ['password' => 'Invalid password']]);
            }

            $token = $this->jwtService->generateToken(['email' => $userFound->email]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['token' => $token]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
