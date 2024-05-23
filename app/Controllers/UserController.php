<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\User;
use App\Services\JwtService;

class UserController extends BaseController
{
    private $validation;
    private $user;
    private $jwtService;

    public function __construct()
    {
        $this->user = new User();
        $this->validation = \Config\Services::validation();
        $this->jwtService = new JwtService();
    }

    public function create()
    {
        $data = $this->request->getJSON();

        if(!$this->validate($this->user->getValidationRules(), $this->user->getValidationMessages())) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON($this->validation->getErrors());
        }

        try {
            $data->password = password_hash($data->password, PASSWORD_DEFAULT);
            if(!$this->user->insert($data)){
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => 'Failed to create user']);
            }   
            $token = $this->jwtService->generateToken(['email' => $data->email]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON(['token' => $token]);         
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error' , 'error' =>$e->getMessage()]);
        }
    }
}
