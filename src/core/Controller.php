<?php

declare(strict_types=1);

namespace Steamy\Core;

trait Controller
{
    /**
     * Renders a view and links its respective CSS file if any.
     *
     * @param string $view_name File name of view file in `views` folder WITHOUT the `.php` extension.
     * @param array $view_data Additional data defined in the view.
     * @param string $template_title Page title. Default value is `Steamy Sips`.
     * @param string $template_tags Additional tags to be included in head. Examples can be
     * script tags and links to other stylesheets.
     *
     * ! Any links used inside $template_tags should be absolute (include ROOT).
     * @return void
     */
    public function view(
        string $view_name,
        array $view_data = [],
        string $template_title = 'Steamy Sips',
        string $template_tags = ''
    ): void {
        // import data to be placed in view file
        if (!empty($view_data)) {
            extract($view_data);
        }

        // convert view name to uppercase
        $view_name = ucfirst($view_name);

        // ! All file paths defined below are relative to public/index.php
        $view_file_path = '../src/views/' . $view_name . '.php';
        $view_relative_css_path = "styles/views/" . $view_name . ".css"; // relative path to css file
        $view_absolute_css_path = ROOT . "/" . $view_relative_css_path; // absolute URL to css stylesheet

        // add link tag for stylesheet if it exists
        if (file_exists($view_relative_css_path)) {
            $template_tags .= "<link rel='stylesheet' href='$view_absolute_css_path'>";
        }

        // get content from view file to be placed in global view template
        $template_content = ''; // html content for template
        ob_start();
        if (file_exists($view_file_path)) {
            include $view_file_path;
        } else {
            include '../src/views/404.php';
        }
        $template_content = ob_get_contents();
        ob_end_clean();

        // display global view template
        require "../src/views/Template.php";
    }
}
