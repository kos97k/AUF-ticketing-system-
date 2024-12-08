<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Requester extends BaseModel
{
    public function findOrCreate($name, $email, $phone)
    {
        $sql = "SELECT id FROM requester WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $requester = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($requester) {
            return $requester['id'];
        }

        $sql = "INSERT INTO requester (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name, ':email' => $email, ':phone' => $phone]);

        return $this->db->lastInsertId();
    }
}
