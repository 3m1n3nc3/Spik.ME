<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPackageUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $package_id = isset($this->params[0]) ? $this->params[0] : false;

        /* Make sure it is either the trial / free package or normal packages */
        switch($package_id) {

            case 'free':

                /* Get the current settings for the free package */
                $package = $this->settings->package_free;

                break;

            case 'trial':

                /* Get the current settings for the trial package */
                $package = $this->settings->package_trial;

                break;

            default:

                $package_id = (int) $package_id;

                /* Check if package exists */
                if(!$package = Database::get('*', 'packages', ['package_id' => $package_id])) {
                    redirect('admin/packages');
                }

                /* Parse the settings of the package */
                $package->settings = json_decode($package->settings);

                break;

        }

        if(!empty($_POST)) {

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            /* Filter variables */
            $_POST['settings'] = [
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
                'monthly_plan_id'       => (string) $_POST['monthly_plan_id'],
                'annual_plan_id'        => (string) $_POST['annual_plan_id'],
            ];

            switch ($package_id) {

                case 'free':

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['status'] = (int) $_POST['status'];

                    /* Make sure to not let the admin disable ALL the packages */
                    if(!$_POST['status']) {

                        $enabled_packages = (int) $this->settings->payment->is_enabled ? Database::$database->query("SELECT COUNT(*) AS `total` FROM `packages` WHERE `status` = 1")->fetch_object()->total ?? 0 : 0;

                        if(!$enabled_packages && !$this->settings->package_trial->status) {
                            $_SESSION['error'][] = $this->language->admin_package_update->error_message->disabled_packages;
                        }
                    }

                    $setting_key = 'package_free';
                    $setting_value = json_encode([
                        'package_id' => 'free',
                        'name' => $_POST['name'],
                        'days' => null,
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                case 'trial':

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['days'] = (int)$_POST['days'];
                    $_POST['status'] = (int)$_POST['status'];

                    /* Make sure to not let the admin disable ALL the packages */
                    if(!$_POST['status']) {

                        $enabled_packages = (int) $this->settings->payment->is_enabled ? Database::$database->query("SELECT COUNT(*) AS `total` FROM `packages` WHERE `status` = 1")->fetch_object()->total ?? 0 : 0;

                        if(!$enabled_packages && !$this->settings->package_free->status) {
                            $_SESSION['error'][] = $this->language->admin_package_update->error_message->disabled_packages;
                        }
                    }

                    $setting_key = 'package_trial';
                    $setting_value = json_encode([
                        'package_id' => 'trial',
                        'name' => $_POST['name'],
                        'days' => $_POST['days'],
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                default:

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['monthly_price'] = (float)$_POST['monthly_price'];
                    $_POST['annual_price'] = (float)$_POST['annual_price'];
                    $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];
                    $_POST['status'] = (int)$_POST['status'];

                    /* Make sure to not let the admin disable ALL the packages */
                    if(!$_POST['status']) {

                        $enabled_packages = (int) Database::$database->query("SELECT COUNT(*) AS `total` FROM `packages` WHERE `status` = 1")->fetch_object()->total ?? 0;

                        if(
                            (
                                !$enabled_packages ||
                                ($enabled_packages == 1 && $package->status))
                            && !$this->settings->package_free->status
                            && !$this->settings->package_trial->status
                        ) {
                            $_SESSION['error'][] = $this->language->admin_package_update->error_message->disabled_packages;
                        }
                    }

                    break;

            }


            if(empty($_SESSION['error'])) {

                /* Update the plan in database */
                switch ($package_id) {

                    case 'free':
                    case 'trial':

                        $stmt = Database::$database->prepare("UPDATE `settings` SET `value` = ? WHERE `key` = ?");
                        $stmt->bind_param('ss', $setting_value, $setting_key);
                        $stmt->execute();
                        $stmt->close();

                        /* Clear the cache */
                        \Altum\Cache::$adapter->deleteItem('settings');

                        break;

                    default:

                        $settings = json_encode($_POST['settings']);

                        $stmt = Database::$database->prepare("UPDATE `packages` SET `name` = ?, `monthly_price` = ?, `annual_price` = ?, `lifetime_price` = ?, `settings` = ?, `status` = ? WHERE `package_id` = ?");
                        $stmt->bind_param('sssssss', $_POST['name'], $_POST['monthly_price'], $_POST['annual_price'], $_POST['lifetime_price'], $settings, $_POST['status'], $package_id);
                        $stmt->execute();
                        $stmt->close();

                        break;

                }

                /* Update all users plan settings with these ones */
                if(isset($_POST['submit_update_users_package_settings'])) {

                    $package_settings = json_encode($_POST['settings']);

                    $stmt = Database::$database->prepare("UPDATE `users` SET `package_settings` = ? WHERE `package_id` = ?");
                    $stmt->bind_param('ss', $package_settings, $package_id);
                    $stmt->execute();
                    $stmt->close();

                }

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                /* Refresh the page */
                redirect('admin/package-update/' . $package_id);

            }

        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/packages/package_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'package_id'    => $package_id,
            'package'       => $package,
        ];

        $view = new \Altum\Views\View('admin/package-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
