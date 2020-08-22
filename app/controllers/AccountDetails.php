<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Altum\Routing\Router;

class AccountDetails extends Controller {

    public function index() {

        Authentication::guard();

        /* Get the transactions if any  */
        $payments_result = Database::$database->query("SELECT * FROM `payments` WHERE `user_id` = {$this->user->user_id} ORDER BY `id` DESC");

        /* Get last X logs */
        $logs_result = Database::$database->query("SELECT * FROM `users_logs` WHERE `user_id` = {$this->user->user_id} ORDER BY `id` DESC LIMIT 15");

        /* Prepare the View */
        $data = [
            'payments_result'    => $payments_result,
            'logs_result'        => $logs_result
        ];

        $view = new \Altum\Views\View('account-details/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
