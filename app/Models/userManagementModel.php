<?php

namespace App\Models;

use CodeIgniter\Model;

class userManagementModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Adjust these to match your actual `users` table columns
    protected $allowedFields = [
        'employee',
        'role',
        'username',
        'email',
        'password',
        'status',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'employee' => 'required|min_length[2]|max_length[255]',
        'role'     => 'required',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'permit_empty|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Hash password before insert/update if present in the data set.
     */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Don't overwrite existing password with blank on update
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Get all users ordered by most recent first.
     */
    public function getAllUsers()
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Find a single user by username.
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }
}