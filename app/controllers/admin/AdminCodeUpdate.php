<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminCodeUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $code_id = isset($this->params[0]) ? $this->params[0] : false;

        if(!$code = Database::get('*', 'codes', ['code_id' => $code_id])) {
            redirect('admin/codes');
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['type'] = in_array($_POST['type'], ['discount', 'redeemable']) ? Database::clean_string($_POST['type']) : 'discount';
            $_POST['days'] = $_POST['type'] == 'redeemable' ? (int) $_POST['days'] : null;
            $_POST['package_id'] = empty($_POST['package_id']) ? null : (int) $_POST['package_id'];
            $_POST['discount'] = $_POST['type'] == 'redeemable' ? 100 : (int) $_POST['discount'];
            $_POST['quantity'] = (int) $_POST['quantity'];

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                $stmt = $this->database->prepare("UPDATE `codes` SET `type` = ?, `days` = ?, `package_id` = ?, `code` = ?, `discount` = ?, `quantity` = ? WHERE `code_id` = ?");
                $stmt->bind_param('sssssss', $_POST['type'], $_POST['days'], $_POST['package_id'], $_POST['code'], $_POST['discount'], $_POST['quantity'], $code_id);
                $stmt->execute();
                $stmt->close();


                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                /* Refresh the page */
                redirect('admin/code-update/' . $code_id);

            }

        }

        $packages_result = $this->database->query("SELECT `package_id`, `name` FROM `packages` WHERE `status` = 1");

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/codes/code_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'code_id'       => $code_id,
            'code'          => $code,
            'packages_result'  => $packages_result
        ];

        $view = new \Altum\Views\View('admin/code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
