<?php

namespace App\Models;

use PDO;

class TeamMember extends BaseModel
{
    public function addTeamMember($userId, $teamId)
    {
        $sql = "INSERT INTO team_member (user, team) VALUES (:user, :team)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $userId, 'team' => $teamId]);
    }

    public function getTeamsByUserId($userId)
    {
        $sql = "SELECT t.id, t.name 
                FROM team_member tm
                JOIN team t ON tm.team = t.id
                WHERE tm.user = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMembersByTeamId($teamId)
    {
        $sql = "SELECT u.id, u.name 
                FROM team_member tm
                JOIN users u ON tm.user = u.id
                WHERE tm.team = :teamId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['teamId' => $teamId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
