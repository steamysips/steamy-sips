<?php

class Home
{
    use Controller;
    function index()
    {
        $data['users'] = (new User)->all();
        $this->view('Home', $data);
    }
}
