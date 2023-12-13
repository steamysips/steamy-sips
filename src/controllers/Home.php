<?php

class Home
{
    use Controller;
    function index()
    {
        $user = new User("john", "aaaaa");

        // $arr['id'] = 1;
        $arr['name'] = 'roti';
        $arr['password'] = 'peit';

        $user->insert($arr);
        // show($result);

        $this->view('Home');
    }
}
