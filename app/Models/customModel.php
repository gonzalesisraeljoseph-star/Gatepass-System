<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class customModel
{
    protected $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function getAllDepartments()
    {
        return $this->db->table('tbl_department')->get()->getResultArray();
    }
}

class departmentModel extends Model
{
    protected $table            = 'tbl_department';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    



    // Adjust these to match your actual `users` table columns
    protected $allowedFields = [
        'name',
        'description',
        
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    public function getAllDepartmentDetails()
    {
        $db = \Config\Database::connect('db_gatepass');

        return $db->table('tbl_department d')
            ->select("d.id, d.description, d.name,")
            ->orderBy('d.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Find a single user by username.
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }
    
}