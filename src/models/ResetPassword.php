<?php
declare(strict_types=1);

namespace Steamy\Model;

use Steamy\Core\Model;

class ResetPassword {
    private Model $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function deleteEmail($email) {
        // Assuming user_id is associated with the email
        $user = $this->model->first(['email' => $email], 'user');
        if ($user) {
            $this->model->delete($user->user_id, 'password_change_requests', 'user_id');
        }
    }

    public function insertToken($email, $date) {
        // Assuming user_id is associated with the email
        $user = $this->model->first(['email' => $email], 'user');
        if ($user) {
            $data = [
                'date' => $date,
                'user_id' => $user->user_id
            ];
            $this->model->insert($data, 'password_change_requests');
        }
    }

    public function resetPassword($userId) {
        $data = [
            'user_id' => $userId
        ];
        return $this->model->first($data, 'password_change_requests');
    }
}
?>
