<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Team extends BaseModel
{
    public function getAllTeams()
    {
        $sql = "SELECT * FROM team ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTeam($name)
    {
        $sql = "INSERT INTO team (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);
        return $this->db->lastInsertId();
    }

    public function deleteTeam($id)
    {
        $sql = "DELETE FROM team WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function updateTeamName($id, $name)
{
    $sql = "UPDATE team SET name = :name, updated_at = NOW() WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);

    return $stmt->execute();
}


    public function getTeamById($id)
    {
        $sql = "SELECT * FROM team WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
