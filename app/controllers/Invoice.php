<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Package;

class Invoice extends Controller {

    public function index() {

        Authentication::guard();

        $id = isset($this->params[0]) ? (int) $this->params[0] : false;

        /* Make sure the campaign exists and is accessible to the user */
        if(!$payment = Database::get('*', 'payments', ['id' => $id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        /* Get the package details */
        $payment->package = (new Package(['settings' => $this->settings]))->get_package_by_id($payment->package_id);

        /* Prepare the View */
        $data = [
            'payment' => $payment
        ];

        $view = new \Altum\Views\View('invoice/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
