<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\User;

class Dashboard
{
    use Controller;

    public function index(): void
    {
        // if user is unauthenticated, redirect to login page
        session_regenerate_id();
        if (!array_key_exists('user', $_SESSION) || !isset($_SESSION['user'])) {
            Utility::redirect('login');
        }

        $data['users'] = (new User())->all();


        $script_tags = <<<EOL
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"
            integrity="sha512-ZwR1/gSZM3ai6vCdI+LVF1zSq/5HznD3ZSTk7kajkaj4D292NLuduDCO1c/NT8Id+jE58KYLKT7hXnbtryGmMg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        EOL;

        $this->view(
            'Dashboard',
            $data,
            'Dashboard',
            template_tags: $script_tags
        );
    }
}
