<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Student;
use CodeIgniter\HTTP\Response;

class StudentController extends BaseController
{
    private $validation;
    private $student;

    public function __construct()
    {
        $this->student = new Student();
        $this->validation = \Config\Services::validation();
    }

    public function create()
    {
        if (!$this->validate($this->student->getValidationRules(), $this->student->getValidationMessages())) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON($this->validation->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'address' => $this->request->getVar('address'),
            'photo' => $this->request->getFile('photo')
        ];

        try {
            if ($data["photo"]) {
                $data["photo"] = $this->savePhoto($data["photo"]);
            }
            $this->student->insert($data);
            $this->student = $this->student->find($this->student->getInsertID());
            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON($this->student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $this->student = $this->student->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($this->student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function showAll()
    {
        try {
            $this->student = $this->student->findAll();
            return $this->response->setJSON($this->student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->student = new Student();
        try {
            $this->student->delete($id);
            if ($this->student->db->affectedRows() === 0) {
                return $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }

            return $this->response->setStatusCode(Response::HTTP_OK)->setJSON(['message' => 'Student deleted successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'address' => $this->request->getVar('address'),
            'photo' => $this->request->getFile('photo')
        ];

        try {
            $studentData = $this->student->find($id);

            if (!$studentData) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }

            if (!empty($studentData['photo']) && $data['photo']) {
                if (!$this->deletePhoto($studentData['photo'])) {
                    return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Failed to delete photo']);
                }
                $data['photo'] = $this->savePhoto($data['photo']);
            }

            if(!$this->student->update($id, $data)){
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => 'Failed to update student']);
            }

            $updatedStudent = $this->student->find($id);

            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($updatedStudent);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function savePhoto($photo)
    {
        $photoName = $photo->getRandomName();
        $photo->move(WRITEPATH . 'uploads', $photoName);
        return $photoName;
    }

    public function deletePhoto($photo)
    {
        return unlink(WRITEPATH . 'uploads/' . $photo);
    }
}
