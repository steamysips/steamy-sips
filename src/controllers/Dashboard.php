<?php

class Dashboard
{
    use Controller;

    function index(): void
    {

        // check if user is authenticated
        session_regenerate_id();
        if (!isset($_SESSION['user']))      // if there is no valid session
        {
            redirect('login');
        }

        $css_file = ROOT . "/styles/views/Dashboard.css";
        $data['users'] = (new User)->all();

        $this->view('Dashboard', $data, 'Dashboard',
            "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
