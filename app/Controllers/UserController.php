<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\User;

class UserController extends BaseController
{
    private $validation;
    private $user;

    public function __construct()
    {
        $this->user = new User();
        $this->validation = \Config\Services::validation();
    }

    public function create()
    {
        $data = $this->request->getJSON();

        if(!$this->validate($this->user->getValidationRules(), $this->user->getValidationMessages())) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON($this->validation->getErrors());
        }

        try {
            $data->password = password_hash($data->password, PASSWORD_DEFAULT);
            $this->user->insert($data);
            $id = $this->user->getInsertID();
            $this->user = $this->user->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON($this->user);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function showAll()
    {
        try {
            $users = $this->user->findAll();
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($users);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
