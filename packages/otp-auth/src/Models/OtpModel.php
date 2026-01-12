<?php

namespace OtpAuth\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table = 'otp_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['identifier', 'code', 'created_at', 'expires_at', 'attempts', 'is_verified'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    public function createOtp(string $identifier, string $hashedCode, int $expirySeconds)
    {
        $data = [
            'identifier' => $identifier,
            'code' => $hashedCode,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', time() + $expirySeconds),
            'attempts' => 0,
            'is_verified' => 0
        ];

        return $this->insert($data);
    }

    public function findValidOtp(string $identifier)
    {
        return $this->where('identifier', $identifier)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->where('is_verified', 0)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function cleanupExpired()
    {
        return $this->where('expires_at <=', date('Y-m-d H:i:s'))
            ->orWhere('is_verified', 1)
            ->delete();
    }

    public function incrementAttempts(int $id)
    {
        // Using direct query to avoid race conditions if possible, but CI4 model update is fine for this scope
        $otp = $this->find($id);
        if ($otp) {
            return $this->update($id, ['attempts' => $otp['attempts'] + 1]);
        }
        return false;
    }
}
