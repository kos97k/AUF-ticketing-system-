<?php

namespace App\Traits;

trait Renderable
{
    public function render($template, $data = [])
    {
        global $mustache;

        // Ensure session is started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Pass user and role data if available
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];
            $data['user']['isAdmin'] = $_SESSION['user']['role'] === 'admin';
            $data['user']['isMember'] = $_SESSION['user']['role'] === 'member';
        } else {
            // Ensure `user` key exists even when no user is logged in
            $data['user'] = [
                'isAdmin' => false,
                'isMember' => false
            ];
        }

        // Render the Mustache template
        $tpl = $mustache->loadTemplate($template);
        echo $tpl->render($data);
    }
}
