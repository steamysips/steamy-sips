<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

/**
 * Controller for handling errors.
 * Script execution is terminated when methods of this controller are called.
 */
class Error
{
    use Controller;

    private array $view_data;

    public function __construct()
    {
        $this->view_data['error_message'] = '500 - Internal server error';
        $this->view_data['extended_error_message'] = 'Something bad happened';
    }

    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
    public function handleDatabaseError(): void
    {
        $this->view_data['extended_error_message'] = 'Unable to connect to database.';

        $this->view(
            'Error',
            $this->view_data,
            template_title: "Error",
            enableIndexing: false
        );
        die();
    }

    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
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
        die();
    }

    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
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
        die();
    }


    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
    public function handleMailingError(): void
    {
        $this->view_data['extended_error_message'] = 'Email could not be sent. Please try again later.';

        $this->view(
            'Error',
            $this->view_data,
            template_title: "Mailing service unavailable",
            enableIndexing: false
        );
        die();
    }

    public function index(): void
    {
        $this->handlePageNotFoundError();
    }
}
