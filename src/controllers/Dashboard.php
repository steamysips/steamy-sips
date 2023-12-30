<?php

class Dashboard
{
    use Controller;

    function index(): void
    {
        $css_file = ROOT . "/styles/views/Dashboard.css";
        $data['users'] = (new User)->all();

        $this->view('Dashboard', $data, 'Dashboard', "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
