<?php

namespace App\Models;

use CodeIgniter\Model;

class Student extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'phone', 'address', 'photo'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'    => 'required|min_length[3]|max_length[100]',
        'email'   => 'required|valid_email|is_unique[students.email]',
        'phone'   => 'required|min_length[10]|max_length[15]',
        'address' => 'required|min_length[10]|max_length[255]',
    ];
    protected $validationMessages   = [
        'name'    => [
            'required'    => 'Name field is required',
            'min_length'  => 'Name field must be at least 3 characters in length',
            'max_length'  => 'Name field must not exceed 100 characters in length',
        ],
        'email'   => [
            'required'    => 'Email field is required',
            'valid_email' => 'Email field must be a valid email address',
            'is_unique'   => 'Email field must be unique',
        ],
        'phone'   => [
            'required'    => 'Phone field is required',
            'min_length'  => 'Phone field must be at least 10 characters in length',
            'max_length'  => 'Phone field must not exceed 15 characters in length',
        ],
        'address' => [
            'required'    => 'Address field is required',
            'min_length'  => 'Address field must be at least 10 characters in length',
            'max_length'  => 'Address field must not exceed 255 characters in length',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
