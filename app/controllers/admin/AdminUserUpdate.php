<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Middlewares\Authentication;

class AdminUserUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $user_id = (isset($this->params[0])) ? $this->params[0] : false;

        /* Check if user exists */
        if(!$user = Database::get('*', 'users', ['user_id' => $user_id])) {
            $_SESSION['error'][] = $this->language->admin_user_update->error_message->invalid_account;
            redirect('admin/users');
        }

        /* Get current package proper details */
        $user->package = (new Package(['settings' => $this->settings]))->get_package_by_id($user->package_id);

        /* Check if its a custom package */
        if($user->package->package_id == 'custom') {
            $user->package->settings = json_decode($user->package_settings);
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['is_enabled']	= (int) $_POST['is_enabled'];
            $_POST['type']	    = (int) $_POST['type'];
            $_POST['package_trial_done'] = (int) $_POST['package_trial_done'];

            switch($_POST['package_id']) {
                case 'free':

                    $package_settings = json_encode($this->settings->package_free->settings);

                    break;

                case 'trial':

                    $package_settings = json_encode($this->settings->package_trial->settings);

                    break;

                case 'custom':

                    $package_settings = json_encode([
                        'additional_global_domains' => (bool) isset($_POST['additional_global_domains']),
                        'custom_url'            => (bool) isset($_POST['custom_url']),
                        'deep_links'            => (bool) isset($_POST['deep_links']),
                        'no_ads'                => (bool) isset($_POST['no_ads']),
                        'removable_branding'    => (bool) isset($_POST['removable_branding']),
                        'custom_branding'       => (bool) isset($_POST['custom_branding']),
                        'custom_colored_links'  => (bool) isset($_POST['custom_colored_links']),
                        'statistics'            => (bool) isset($_POST['statistics']),
                        'google_analytics'      => (bool) isset($_POST['google_analytics']),
                        'facebook_pixel'        => (bool) isset($_POST['facebook_pixel']),
                        'custom_backgrounds'    => (bool) isset($_POST['custom_backgrounds']),
                        'verified'              => (bool) isset($_POST['verified']),
                        'scheduling'            => (bool) isset($_POST['scheduling']),
                        'seo'                   => (bool) isset($_POST['seo']),
                        'utm'                   => (bool) isset($_POST['utm']),
                        'socials'               => (bool) isset($_POST['socials']),
                        'fonts'                 => (bool) isset($_POST['fonts']),
                        'projects_limit'        => (int) $_POST['projects_limit'],
                        'biolinks_limit'        => (int) $_POST['biolinks_limit'],
                        'links_limit'           => (int) $_POST['links_limit'],
                        'domains_limit'         => (int) $_POST['domains_limit'],
                    ]);

                    break;

                default:

                    $_POST['package_id'] = (int) $_POST['package_id'];

                    /* Make sure this package exists */
                    if(!$package_settings = Database::simple_get('settings', 'packages', ['package_id' => $_POST['package_id']])) {
                        redirect('admin/user-update/' . $user->user_id);
                    }

                    break;
            }

            $_POST['package_expiration_date'] = (new \DateTime($_POST['package_expiration_date']))->format('Y-m-d H:i:s');

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
                $_SESSION['error'][] = $this->language->admin_user_update->error_message->name_length;
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                $_SESSION['error'][] = $this->language->admin_user_update->error_message->invalid_email;
            }

            if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== Database::simple_get('email', 'users', ['user_id' => $user->user_id])) {
                $_SESSION['error'][] = $this->language->admin_user_update->error_message->email_exists;
            }

            if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                if(strlen(trim($_POST['new_password'])) < 6) {
                    $_SESSION['error'][] = $this->language->admin_user_update->error_message->short_password;
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    $_SESSION['error'][] = $this->language->admin_user_update->error_message->passwords_not_matching;
                }
            }


            if(empty($_SESSION['error'])) {

                /* Update the basic user settings */
                $stmt = Database::$database->prepare("
                    UPDATE
                        `users`
                    SET
                        `name` = ?,
                        `email` = ?,
                        `active` = ?,
                        `type` = ?,
                        `package_id` = ?,
                        `package_expiration_date` = ?,
                        `package_settings` = ?,
                        `package_trial_done` = ?
                    WHERE
                        `user_id` = ?
                ");
                $stmt->bind_param(
                    'sssssssss',
                    $_POST['name'],
                    $_POST['email'],
                    $_POST['is_enabled'],
                    $_POST['type'],
                    $_POST['package_id'],
                    $_POST['package_expiration_date'],
                    $package_settings,
                    $_POST['package_trial_done'],
                    $user->user_id
                );
                $stmt->execute();
                $stmt->close();

                /* Update the password if set */
                if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    $stmt = Database::$database->prepare("UPDATE `users` SET `password` = ?  WHERE `user_id` = {$user->user_id}");
                    $stmt->bind_param('s', $new_password);
                    $stmt->execute();
                    $stmt->close();
                }

                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/user-update/' . $user->user_id);
            }

        }

        /* Login Modal */
        $view = new \Altum\Views\View('admin/users/user_login_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/users/user_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Get all the packages available */
        $packages_result = Database::$database->query("SELECT * FROM `packages` WHERE `status` = 1");

        /* Main View */
        $data = [
            'user'              => $user,
            'packages_result'   => $packages_result,
        ];

        $view = new \Altum\Views\View('admin/user-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
