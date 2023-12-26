<?php

trait Controller
{


    /**
     * Renders a view
     *
     * @param  mixed $filename File name of view file in `views` folder without the `.php` extension.
     * @param  mixed $data  Additional data defined in the view.
     * @param  mixed $template_title Page title. Default value is `lamp`.
     * @param  mixed $template_tags Optional tags to be included in head. Examples can be
     * script tags and links to stylesheets.
     *
     * ! Any links used inside $template_tags should be absolute (include ROOT).
     * @return void
     */
    public function view(string $filename, array $data = [], string $template_title = 'lamp', string $template_tags = '')
    {
        if (!empty($data)) {
            extract($data);
        }
        $template_filename = '../src/views/' . ucfirst($filename) . '.php';
        $template_content = ''; // html content for template

        ob_start();
        if (file_exists($template_filename)) {
            include($template_filename);
        } else {
            include('../src/views/404.php');
        }
        $template_content = ob_get_contents();
        ob_end_clean();

        $template_filename = "../src/views/Template.php";
        require $template_filename;
    }
}
