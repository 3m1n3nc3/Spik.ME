<?php defined('ALTUMCODE') || die() ?>

<?php

use Altum\Middlewares\Authentication;

?>

<?php if($this->settings->payment->is_enabled): ?>

    <?php
    $packages = [];
    $available_payment_frequencies = [];

    $packages_result = $this->database->query("SELECT * FROM `packages` WHERE `status` = 1");

    while($package = $packages_result->fetch_object()) {
        $packages[] = $package;

        foreach(['monthly', 'annual', 'lifetime'] as $value) {
            if($package->{$value . '_price'}) {
                $available_payment_frequencies[$value] = true;
            }
        }
    }

    ?>

    <?php if(count($packages)): ?>
        <div class="mb-5 d-flex justify-content-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">

                <?php if(isset($available_payment_frequencies['monthly'])): ?>
                    <label class="btn btn-outline-secondary active" data-payment-frequency="monthly">
                        <input type="radio" name="payment_frequency" checked> <?= $this->language->package->custom_package->monthly ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['annual'])): ?>
                    <label class="btn btn-outline-secondary" data-payment-frequency="annual">
                        <input type="radio" name="payment_frequency"> <?= $this->language->package->custom_package->annual ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['lifetime'])): ?>
                    <label class="btn btn-outline-secondary" data-payment-frequency="lifetime">
                        <input type="radio" name="payment_frequency"> <?= $this->language->package->custom_package->lifetime ?>
                    </label>
                <?php endif ?>

            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div class="pricing-container">
    <div class="pricing">

        <?php if($this->settings->package_free->status == 1): ?>

            <div class="pricing-plan shadow-lg">
                <div class="pricing-header">
                    <span class="pricing-name"><?= $this->settings->package_free->name ?></span>

                    <div class="pricing-price">
                        <span class="pricing-price-amount"><?= $this->language->package->free->price ?></span>
                    </div>

                    <div class="pricing-details">&nbsp;</div>
                </div>

                <div class="pricing-body">
                    <ul class="pricing-features">
                        <?php if($this->settings->package_free->settings->projects_limit == -1): ?>
                            <li>
                                <div><?= $this->language->global->package_settings->unlimited_projects_limit ?></div>
                            </li>
                        <?php else: ?>
                            <li>
                                <div><?= sprintf($this->language->global->package_settings->projects_limit, $this->settings->package_free->settings->projects_limit) ?></div>
                            </li>
                        <?php endif ?>

                        <?php if($this->settings->package_free->settings->biolinks_limit == -1): ?>
                            <li>
                                <div><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></div>
                            </li>
                        <?php else: ?>
                            <li>
                                <div><?= sprintf($this->language->global->package_settings->biolinks_limit, $this->settings->package_free->settings->biolinks_limit) ?></div>
                            </li>
                        <?php endif ?>

                        <?php if($this->settings->links->shortener_is_enabled): ?>
                            <?php if($this->settings->package_free->settings->links_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_links_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->links_limit, $this->settings->package_free->settings->links_limit) ?></div>
                                </li>
                            <?php endif ?>
                        <?php endif ?>

                        <?php if($this->settings->links->domains_is_enabled): ?>
                            <?php if($this->settings->package_free->settings->domains_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_domains_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->domains_limit, $this->settings->package_free->settings->domains_limit) ?></div>
                                </li>
                            <?php endif ?>
                        <?php endif ?>

                        <?php foreach($data->simple_user_package_settings as $package_setting): ?>
                            <li>
                                <div class="<?= $this->settings->package_free->settings->{$package_setting} ? null : 'text-muted' ?>">
                                    <span data-toggle="tooltip" title="<?= $this->language->global->package_settings->{$package_setting . '_help'} ?>"><?= $this->language->global->package_settings->{$package_setting} ?></span>
                                </div>

                                <i class="fa fa-fw fa-sm <?= $this->settings->package_free->settings->{$package_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                            </li>
                        <?php endforeach ?>
                    </ul>

                    <?php if(Authentication::check() && $this->user->package_id == 'free'): ?>
                        <button class="btn btn-lg btn-block btn-secondary pricing-button"><?= $this->language->package->button->already_free ?></button>
                    <?php else: ?>
                        <a href="<?= Authentication::check() ? url('pay/free') : url('register?redirect=pay/free') ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->package->button->choose ?></a>
                    <?php endif ?>
                </div>
            </div>

        <?php endif ?>

        <?php if($this->settings->payment->is_enabled): ?>

            <?php if($this->settings->package_trial->status == 1): ?>

                <div class="pricing-plan shadow-lg">
                    <div class="pricing-header">
                        <span class="pricing-name"><?= $this->settings->package_trial->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount"><?= $this->language->package->trial->price ?></span>
                        </div>

                        <div class="pricing-details">&nbsp;</div>
                    </div>

                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <?php if($this->settings->package_trial->settings->projects_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_projects_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->projects_limit, $this->settings->package_trial->settings->projects_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->package_trial->settings->biolinks_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->biolinks_limit, $this->settings->package_trial->settings->biolinks_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->links->shortener_is_enabled): ?>
                                <?php if($this->settings->package_trial->settings->links_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->package_settings->unlimited_links_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->package_settings->links_limit, $this->settings->package_trial->settings->links_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if($this->settings->links->domains_is_enabled): ?>
                                <?php if($this->settings->package_trial->settings->domains_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->package_settings->unlimited_domains_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->package_settings->domains_limit, $this->settings->package_trial->settings->domains_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php foreach($data->simple_user_package_settings as $package_setting): ?>
                                <li>
                                    <div class="<?= $this->settings->package_trial->settings->{$package_setting} ? null : 'text-muted' ?>">
                                                        <span data-toggle="tooltip" title="<?= $this->language->global->package_settings->{$package_setting . '_help'} ?>"><?= $this->language->global->package_settings->{$package_setting} ?></span>
                                    </div>

                                    <i class="fa fa-fw fa-sm <?= $this->settings->package_trial->settings->{$package_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                                </li>
                            <?php endforeach ?>
                        </ul>

                        <?php if(Authentication::check() && $this->user->package_trial_done): ?>
                            <button class="btn btn-lg btn-block btn-secondary pricing-button"><?= $this->language->package->button->disabled ?></button>
                        <?php else: ?>
                            <a href="<?= Authentication::check() ? url('pay/trial') : url('register?redirect=pay/trial') ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->package->button->choose ?></a>
                        <?php endif ?>
                    </div>
                </div>

            <?php endif ?>

            <?php foreach($packages as $package): ?>

                <?php $package->settings = json_decode($package->settings) ?>

                <div
                    class="pricing-plan shadow-lg"
                    data-package-monthly="<?= json_encode((bool) $package->monthly_price) ?>"
                    data-package-annual="<?= json_encode((bool) $package->annual_price) ?>"
                    data-package-lifetime="<?= json_encode((bool) $package->lifetime_price) ?>"
                >
                    <div class="pricing-header">
                        <span class="pricing-name"><?= $package->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount d-none" data-package-payment-frequency="monthly"><?= $package->monthly_price ?></span>
                            <span class="pricing-price-amount d-none" data-package-payment-frequency="annual"><?= $package->annual_price ?></span>
                            <span class="pricing-price-amount d-none" data-package-payment-frequency="lifetime"><?= $package->lifetime_price ?></span>
                            <span class="pricing-price-currency"><?= $this->settings->payment->currency ?></span>
                        </div>

                        <div class="pricing-details">
                            <span class="d-none" data-package-payment-frequency="monthly"><?= $this->language->package->custom_package->monthly_payments ?></span>
                            <span class="d-none" data-package-payment-frequency="annual"><?= $this->language->package->custom_package->annual_payments ?></span>
                            <span class="d-none" data-package-payment-frequency="lifetime"><?= $this->language->package->custom_package->lifetime_payments ?></span>
                        </div>
                    </div>

                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <?php if($package->settings->projects_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_projects_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->projects_limit, $package->settings->projects_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($package->settings->biolinks_limit == -1): ?>
                                <li>
                                    <div><?= $this->language->global->package_settings->unlimited_biolinks_limit ?></div>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div><?= sprintf($this->language->global->package_settings->biolinks_limit, $package->settings->biolinks_limit) ?></div>
                                </li>
                            <?php endif ?>

                            <?php if($this->settings->links->shortener_is_enabled): ?>
                                <?php if($package->settings->links_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->package_settings->unlimited_links_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->package_settings->links_limit, $package->settings->links_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if($this->settings->links->domains_is_enabled): ?>
                                <?php if($package->settings->domains_limit == -1): ?>
                                    <li>
                                        <div><?= $this->language->global->package_settings->unlimited_domains_limit ?></div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div><?= sprintf($this->language->global->package_settings->domains_limit, $package->settings->domains_limit) ?></div>
                                    </li>
                                <?php endif ?>
                            <?php endif ?>

                            <?php foreach($data->simple_user_package_settings as $package_setting): ?>
                                <li>
                                    <div class="<?= $package->settings->{$package_setting} ? null : 'text-muted' ?>">
                                        <span data-toggle="tooltip" title="<?= $this->language->global->package_settings->{$package_setting . '_help'} ?>"><?= $this->language->global->package_settings->{$package_setting} ?></span>
                                    </div>

                                    <i class="fa fa-fw fa-sm <?= $package->settings->{$package_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                                </li>
                            <?php endforeach ?>
                        </ul>

                        <a href="<?= Authentication::check() ? url('pay/' . $package->package_id) : url('register?redirect=pay/' . $package->package_id) ?>" class="btn btn-lg btn-block btn-primary pricing-button"><?= $this->language->package->button->choose ?></a>
                    </div>
                </div>

            <?php endforeach ?>

            <?php ob_start() ?>
            <script>
                'use strict';

                let payment_frequency_handler = (event = null) => {

                    let payment_frequency = null;

                    if(event) {
                        payment_frequency = $(event.currentTarget).data('payment-frequency');
                    } else {
                        payment_frequency = $('[name="payment_frequency"]:checked').closest('label').data('payment-frequency');
                    }

                    switch(payment_frequency) {
                        case 'monthly':
                            $(`[data-package-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-package-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break;

                        case 'annual':
                            $(`[data-package-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-package-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break

                        case 'lifetime':
                            $(`[data-package-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-package-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');

                            break
                    }

                    $(`[data-package-payment-frequency="${payment_frequency}"]`).addClass('d-inline-block');

                    $(`[data-package-${payment_frequency}="true"]`).removeClass('d-none').addClass('');
                    $(`[data-package-${payment_frequency}="false"]`).addClass('d-none').removeClass('');

                };

                $('[data-payment-frequency]').on('click', payment_frequency_handler);

                payment_frequency_handler();
            </script>
            <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <?php endif ?>

    </div>
</div>











