<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Student;
use CodeIgniter\HTTP\Response;

class StudentController extends BaseController
{
    public function create()
    {
        $studentModel = new Student();
        $data = $this->request->getJSON();

        try {
            $studentModel->insert($data);
            $id = $studentModel->getInsertID();
            $student = $studentModel->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON($student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $studentModel = new Student();
        try {
            $student = $studentModel->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function showAll()
    {
        $studentModel = new Student();
        try {
            $students = $studentModel->findAll();
            return $this->response->setJSON($students);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $studentModel = new Student();
        try {
            $studentModel->delete($id);
            if($studentModel->db->affectedRows() === 0) {
                return $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }

            return $this->response->setStatusCode(Response::HTTP_OK)->setJSON(['message' => 'Student deleted successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $studentModel = new Student();
        $data = $this->request->getJSON();

        try {
            $studentModel->update($id, $data);
            if(!$student = $studentModel->find($id)){
                return $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
