<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\User;

class UserController extends BaseController
{
    public function create()
    {
        $user = new User();
        $data = $this->request->getJSON();
        try {
            $data->password = password_hash($data->password, PASSWORD_DEFAULT);
            $user->insert($data);
            $id = $user->getInsertID();
            $user = $user->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON($user);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function showAll()
    {
        $user = new User();
        
        try {
            $users = $user->findAll();
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($users);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
