<?php

class Home
{
    use Controller;

    public function index(): void
    {
        $home_css_path = ROOT . "/styles/views/Home.css";
        $home_css_tag = "<link rel=\"stylesheet\" href=\"$home_css_path\"/>";
        $this->view('Home', [], 'Home', $home_css_tag);
    }
}
