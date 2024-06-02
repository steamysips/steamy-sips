<?php

declare(strict_types=1);

namespace Steamy\Controller;

use PHPMailer\PHPMailer\Exception;
use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Mailer;

/**
 * Controller for handling contact us form submission
 */
class Contact
{
    use Controller;

    private array $view_data;

    public function __construct()
    {
        // initialize view data
        $this->view_data['defaultFirstName'] = "";
        $this->view_data['defaultLastName'] = "";
        $this->view_data['defaultEmail'] = "";
        $this->view_data['defaultMessage'] = "";
        $this->view_data['errors'] = [];
        $this->view_data['contact_us_successful'] = false;

    }

    /**
     * Returns the un-sanitized version of the form data.
     * @return array An array containing form data.
     */
    public function getFormData(): array
    {
        $form_data = [];

        $form_data['first_name'] = trim($_POST['first_name'] ?? "");
        $form_data['last_name'] = trim($_POST['last_name'] ?? "");
        $form_data['email'] = filter_var(trim($_POST['email'] ?? ""), FILTER_VALIDATE_EMAIL);
        $form_data['message'] = trim($_POST['message'] ?? "");

        return $form_data;
    }

    private function validateContactForm(): array
    {
        $errors = [];
        $form_data = $this->getFormData();

        // Validate first name
        if (strlen($form_data['first_name']) < 2) {
            $errors['first_name'] = "First name must be at least 2 characters long";
        }

        // Validate last name
        if (strlen($form_data['last_name']) < 2) {
            $errors['last_name'] = "Last name must be at least 2 characters long";
        }

        // Validate email
        if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        // Validate message
        if (strlen($form_data['message']) < 10) {
            $errors['message'] = "Message must be at least 10 characters long";
        }

        return $errors;
    }


    private function handleFormSubmission(): void
    {
        $form_data = $this->getFormData();

        // validate all attributes
        $this->view_data['errors'] = $this->validateContactForm();

        // if no errors and display success message
        if (empty($this->view_data['errors'])) {
            $success = $this->mailBusinessInbox($form_data);
            if ($success) {
                $this->view_data['contact_us_successful'] = true;
            } else {
                (new Error())->handleMailingError();
            }
        } else {
            $this->loadDataToForm($form_data);
        }
    }

    /**
     * Sends an email to the business inbox.
     * @param $form_data
     * @return bool Success or not
     */
    private function mailBusinessInbox($form_data): bool
    {
        // Load the email template
        ob_start();
        extract($form_data);
        include __DIR__ . '/../views/mails/Contact.php';
        $htmlMessage = ob_get_clean();

        $plainMessage = "You have received a new message from:\n";
        $plainMessage .= "Name: " . $form_data['first_name'] . " " . $form_data['last_name'] . "\n";
        $plainMessage .= "Email: " . $form_data['email'] . "\n";
        $plainMessage .= "Message: " . $form_data['message'] . "\n";

        // Implement logic to send email to admin using Mailer class
        $mailer = new Mailer();
        $subject = "New Contact Message from " . $form_data['first_name'] . " " . $form_data['last_name'];

        try {
            $mailer->sendMail($_ENV['BUSINESS_GMAIL'], $subject, $htmlMessage, $plainMessage);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Updates view data with data from form. Invalid data entered by user persists.
     * @param array $form_data
     * @return void
     */
    private function loadDataToForm(array $form_data): void
    {
        $this->view_data['defaultFirstName'] = $form_data['first_name'];
        $this->view_data['defaultLastName'] = $form_data['last_name'];
        $this->view_data['defaultEmail'] = $form_data['email'];
        $this->view_data['defaultMessage'] = $form_data['message'];
    }

    private function validateURL(): bool
    {
        return Utility::getURL() === 'contact';
    }

    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new Error())->handlePageNotFoundError();
            die();
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // form has been submitted
            $this->handleFormSubmission();
        }

        $this->view(
            'Contact',
            $this->view_data,
            'Contact Us',
            template_meta_description: "Get in touch with us! Use this form to send us your queries, feedback,
             or any other message. We'll get back to you as soon as possible.",
        );
    }
}
