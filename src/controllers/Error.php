<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Error
{
    use Controller;

    private array $view_data;

    public function __construct()
    {
        $this->view_data['error_message'] = '500 - Internal server error';
        $this->view_data['extended_error_message'] = 'Something bad happened';
    }

    public function handleDatabaseError(): void
    {
        $this->view_data['extended_error_message'] = 'Unable to connect to database.';

        $this->view(
            'Error',
            $this->view_data,
            template_title: "Error",
            enableIndexing: false
        );
    }

    public function handlePageNotFoundError($extended_error_message = 'Ensure that you have properly typed the URL.'
    ): void {
        $this->view_data['error_message'] = '404 - Page not found';
        $this->view_data['extended_error_message'] = $extended_error_message;

        $this->view(
            'Error',
            $this->view_data,
            template_title: "Page not found",
            enableIndexing: false
        );
    }

    public function handleUnknownError(): void
    {
        $this->view_data['extended_error_message'] = 'The server has encountered a situation 
        it does not know how to handle.';

        $this->view(
            'Error',
            $this->view_data,
            template_title: "Unknown error",
            enableIndexing: false
        );
    }

    public function index(): void
    {
        $this->handlePageNotFoundError();
    }
}
