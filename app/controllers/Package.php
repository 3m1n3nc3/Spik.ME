<?php

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class Package extends Controller {

    public function index() {

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $type = isset($this->params[0]) && in_array($this->params[0], ['renew', 'upgrade', 'new']) ? $this->params[0] : 'new';

        /* If the user is not logged in when trying to upgrade or renew, make sure to redirect them */
        if(in_array($type, ['renew', 'upgrade']) && !Authentication::check()) {
            redirect('package/new');
        }

        /* Packages View */
        $data = [
            'simple_user_package_settings' =>  require APP_PATH . 'includes/simple_user_package_settings.php'
        ];

        $view = new \Altum\Views\View('partials/packages', (array) $this);

        $this->add_view_content('packages', $view->run($data));


        /* Prepare the View */
        $data = [
            'type' => $type
        ];

        $view = new \Altum\Views\View('package/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
