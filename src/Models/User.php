<?php

namespace App\Models;

use PDO;

class User extends BaseModel
{
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (name, email, phone, password, last_password) 
                VALUES (:name, :email, :phone, :password, :last_password)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);    
        return $this->db->lastInsertId();
    }

   public function viewTicket($id)
{
    session_start();

    if (!isset($_SESSION['logged-in']) || !$_SESSION['logged-in']) {
        header('Location: /login-form');
        exit();
    }

    $ticketModel = new \App\Models\Ticket();
    $commentModel = new \App\Models\Comment();

    // Fetch ticket details
    $ticket = $ticketModel->getTicketByIdWithDetails($id);
    if (!$ticket) {
        $_SESSION['err'] = "Ticket not found.";
        header('Location: /dashboard');
        exit();
    }

    // Fetch comments and include user details
    $rawComments = $commentModel::getCommentsByTicketId($id);
    $userModel = new \App\Models\User();
    $comments = array_map(function ($comment) use ($userModel) {
        $user = $userModel->getUserById($comment['user_id']);
        return [
            'comment_text' => $comment['comment_text'],
            'created_at' => $comment['created_at'],
            'user_name' => $user['name'] ?? 'Unknown User',
        ];
    }, $rawComments);

    $template = 'view';
    $data = [
        'title' => 'View Ticket',
        'ticket' => $ticket,
        'comments' => $comments,
        'currentUser' => $_SESSION['user'],
    ];

    echo $this->render($template, $data);
}

    // Add this method to handle login by email
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data)
{
    $sql = "UPDATE users 
            SET name = :name, email = :email, phone = :phone, password = :password 
            WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR);

    return $stmt->execute();
}


    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserById($id)
{
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    

    
}
