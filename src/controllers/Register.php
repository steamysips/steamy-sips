<?php

class Register
{
    use Controller;
    public function index()
    {
        // echo 'this is register controller';

        $this->view('Register');
    }
}
