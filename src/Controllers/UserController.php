<?php

namespace App\Controllers;

use App\Models\User;
use PDO;

class UserController extends BaseController
{
    private $userModel;
    private $db;

    // Default to null or pass the connection directly if available
    public function __construct($conn = null)
    {
        // If no connection is provided, use a default or throw an error
        $this->db = $conn ?: new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $this->userModel = new User($this->db);
    }

    public function showUserTable()
    {
        session_start();
        if (!isset($_SESSION['logged-in'])) {
            header('Location: /login-form');
            exit();
        }

        $users = $this->userModel->getAllUsers();
        $template = 'users';
        $data = ['title' => 'User Table', 'users' => $users, 'user' => $_SESSION['user']];

        echo $this->render($template, $data);
    }

    public function showCreateUsersForm()
    {
        session_start();
        if (!isset($_SESSION['logged-in'])) {
            header('Location: /login-form');
            exit();
        }

        $template = 'create-user';
        $data = ['title' => 'Create User'];
        echo $this->render($template, $data);
    }

    public function storeUser()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                'last_password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
            ];
            $this->userModel->createUser($data);
            header('Location: /users');
            exit();
        }
    }

    public function editUserForm($id)
{
    $userModel = new \App\Models\User();
    $user = $userModel->getUserById($id);

    if ($user) {
        $this->render('edit-user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    } else {
        echo "User not found.";
        exit;
    }
}

public function updateUser($id)
{
    $userModel = new \App\Models\User();

    // Collect form data
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'], // Add phone number
    ];

    // Check if a new password is provided
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    } else {
        // Retain the current password if no new password is provided
        $existingUser = $userModel->getUserById($id);
        $data['password'] = $existingUser['password'];
    }

    // Update the user in the database
    $userModel->updateUser($id, $data);

    // Redirect to the Users page
    header("Location: /users");
    exit;
}



    public function deleteUser($id)
    {
        session_start();
        $this->userModel->deleteUser($id);
        header('Location: /users');
        exit();
    }
    public function viewUser($id)
    {
        $user = $this->userModel->getUserById($id);
        $teamMemberModel = new \App\Models\TeamMember();
        $teams = $teamMemberModel->getTeamsByUserId($id);

        if (!$user) {
            die("User not found.");
        }

        $this->render('view-user', [
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'teams' => $teams
        ]);
    }
}

