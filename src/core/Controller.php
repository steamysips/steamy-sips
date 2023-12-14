<?php

trait Controller
{
    /**
     * Displays view
     */
    public function view($name, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }
        $filename = '../src/views/' . ucfirst($name) . '.php';
        $content = ''; // html content for template
        $title = ucfirst($name); // title for template

        ob_start();
        if (file_exists($filename)) {
            include($filename);
        } else {
            include('../src/views/404.php');
        }
        $content = ob_get_contents();
        ob_end_clean();

        $template_filename = "../src/views/Template.php";
        require $template_filename;
    }
}
