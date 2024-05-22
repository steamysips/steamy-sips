<?php

declare(strict_types=1);

namespace Steamy\Core;

use Steamy\Model\Client;

trait Controller
{

    /**
     * Returns the required HTML code to load JS libraries.
     * @param string[] $required_libraries An array of strings representing the names of the libraries that must be
     * @return string HTML tags to load the library.
     */
    private function getLibrariesTags(array $required_libraries): string
    {
        $library_tags = [];

        $library_tags['aos'] = <<< EOL
        <!-- AOS animation library-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css"
          integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"
            integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        EOL;

        // concatenate all tags for the required libraries
        $script_str = "";
        foreach (array_keys($library_tags) as $library) {
            if (in_array($library, $required_libraries, true)) {
                $script_str .= $library_tags[$library];
            }
        }
        return $script_str;
    }

    /**
     * @return Client|null Client account of currently logged-in user. Null if no one is logged in.
     */
    private function getSignedInClient(): ?Client
    {
        // $_SESSION['user'] was set to the client email on login
        // if it is empty, no one is logged in
        if (empty($_SESSION['user'])) {
            return null;
        }

        return Client::getByEmail($_SESSION['user']);
    }

    /**
     * Renders a view and links its respective CSS file if any.
     *
     * @param string $view_name File name of view file in `views` folder WITHOUT the `.php` extension.
     * @param array $view_data Values for the placeholder data defined in the view.
     * @param string $template_title Page title. Default value is `Steamy Sips`.
     * @param string $template_tags Additional tags to be included in `<head>`. Examples can be
     * script tags and links to other stylesheets.
     * @param string $template_meta_description Meta description of page. Default value is empty.
     * @param bool $enableIndexing Whether page should be indexed by search engines
     * @return void
     */
    public function view(
        string $view_name,
        array $view_data = [],
        string $template_title = 'Steamy Sips',
        string $template_tags = '',
        string $template_meta_description = '',
        bool $enableIndexing = true,
    ): void {
        // check if search engine indexing must be disabled
        if (!$enableIndexing) {
            $template_tags .= <<< EOL
                <meta name="robots" content="noindex, follow, noarchive">
            EOL;
        }

        // import data to be placed in view file
        if (!empty($view_data)) {
            extract($view_data);
        }

        // convert view name to uppercase
        $view_name = ucfirst($view_name);

        $view_file_path = __DIR__ . '/../views/' . $view_name . '.php';
        $view_relative_css_path = "styles/views/" . $view_name . ".css"; // relative path to css file
        $view_absolute_css_path = "/" . $view_relative_css_path; // root-relative URL to css stylesheet

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
            include __DIR__ . '/../views/Error.php';
        }
        $template_content = ob_get_contents();
        ob_end_clean();

        // display global view template
        require_once __DIR__ . "/../views/Template.php";
    }
}
