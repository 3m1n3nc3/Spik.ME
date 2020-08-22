<?php

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class PayThankYou extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $package_id = $_GET['package_id'] ?? null;

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
                if(!$package = (new \Altum\Models\Package(['settings' => $this->settings]))->get_package_by_id($package_id)) {
                    redirect('package');
                }

                break;
        }

        /* Make sure the package is enabled */
        if(!$package->status) {
            redirect('package');
        }

        /* Extra safety */
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return !in_array($key, ['altum', 'unique_transaction_identifier']);
        }, ARRAY_FILTER_USE_KEY);


        $thank_you_url_parameters = '';
        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $unique_transaction_identifier = md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        if($_GET['unique_transaction_identifier'] != $unique_transaction_identifier) {
            redirect('package');
        }

        /* Prepare the View */
        $data = [
            'package_id'    => $package_id,
            'package'       => $package,
        ];

        $view = new \Altum\Views\View('pay-thank-you/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
