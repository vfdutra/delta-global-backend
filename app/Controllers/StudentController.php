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
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'address' => $this->request->getVar('address'),
        ];
    
        $photo = null;
        if ($imageFile = $this->request->getFile('photo')) {
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                $photo = $this->savePhoto($imageFile);
            }

            if (!$photo) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => 'Failed to upload photo', 'errors' => $imageFile->getErrorString()]);
            }

            $data['photo'] = $photo;
        }

        try {            
            if (!$this->validate($this->student->getValidationRules(), $this->student->getValidationMessages())) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON($this->validation->getErrors());
            }
    
            if (!$this->student->insert($data)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => 'Failed to insert student', 'errors' => $this->student->errors()]);
            }
    
            $student = $this->student->find($this->student->getInsertID());

            return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON($student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error', 'error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $this->student = $this->student->find($id);
            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($this->student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error', 'error' => $e->getMessage()]);
        }
    }

    public function showAll()
    {
        try {
            $this->student = $this->student->findAll();
            return $this->response->setJSON($this->student);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error', 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->student->delete($id);
            if ($this->student->db->affectedRows() === 0) {
                return $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }

            return $this->response->setStatusCode(Response::HTTP_OK)->setJSON(['message' => 'Student deleted successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error', 'error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
         // Retrieve the current validation rules
         $validationRules = $this->student->validationRules;

         // Substitute {id} with the actual id
         foreach ($validationRules as $field => $rule) {
             $validationRules[$field] = str_replace('{id}', $id, $rule);
         }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'address' => $this->request->getVar('address'),
        ];

        $newPhotoName = null;
        if ($imageFile = $this->request->getFile('photo')) {
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                $newPhotoName = $this->savePhoto($imageFile);
            }

            if (!$newPhotoName) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['message' => 'Failed to upload photo', 'errors' => $imageFile->getErrorString()]);
            }
        }

        try {
            $studentData = $this->student->find($id);

            if (!$studentData) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
            }

            if (!empty($studentData['photo']) && $newPhotoName) {
                if (!$this->deletePhoto($studentData['photo'])) {
                    return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Failed to delete old photo']);
                }
                $data['photo'] = $newPhotoName;
            } elseif ($newPhotoName) {
                $data['photo'] = $newPhotoName;
            }

            $this->student->setValidationRules($validationRules);

            if (!$this->student->update($id, $data)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON([
                    'message' => 'Failed to update student',
                    'errors' => $this->student->errors(),
                ]);
            }

            $updatedStudent = $this->student->find($id);

            return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON($updatedStudent);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['message' => 'Internal server error', 'error' => $e->getMessage()]);
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

    public function getPhoto($id)
    {
        $student = $this->student->find($id);
        if (!$student) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Student not found']);
        }

        $photo = $student['photo'];
        $path = WRITEPATH . 'uploads/' . $photo;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Photo not found']);
        }

        return $this->response->setContentType('image/jpeg')->setBody(file_get_contents($path));
    }
}
