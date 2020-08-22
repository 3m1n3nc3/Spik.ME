<?php

namespace Altum\Models;

use Altum\Database\Database;

class Package extends Model {

    public function get_package_by_id($packageId) {

        switch($packageId) {

            case 'free':

                return $this->settings->package_free;

                break;

            case 'trial':

                return $this->settings->package_trial;

                break;

            case 'custom':

                return $this->settings->package_custom;

                break;

            default:

                $package = Database::get('*', 'packages', ['package_id' => $packageId]);

                if(!$package) {
                    return $this->settings->package_custom;
                }

                $package->settings = json_decode($package->settings);

                return $package;

                break;

        }

    }

}
