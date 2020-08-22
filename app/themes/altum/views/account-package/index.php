<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container pt-5">

    <?php display_notifications() ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-5">
        <div>
            <h2 class="h3"><?= $this->language->account_package->header ?></h2>
        </div>

        <?php if($this->settings->payment->is_enabled): ?>
            <div class="col-auto p-0">
                <?php if($this->user->package_id == 'free'): ?>
                    <a href="<?= url('package/upgrade') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-arrow-up"></i> <?= $this->language->account->package->upgrade_package ?></a>
                <?php elseif($this->user->package_id == 'trial'): ?>
                    <a href="<?= url('package/renew') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-sync-alt"></i> <?= $this->language->account->package->renew_package ?></a>
                <?php else: ?>
                    <a href="<?= url('package/renew') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-sync-alt"></i> <?= $this->language->account->package->renew_package ?></a>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <h2 class="h3"><?= $this->user->package->name ?></h2>

            <?php if($this->user->package_id != 'free'): ?>
                <p class="text-muted">
                    <?= sprintf(
                        $this->user->payment_subscription_id ? $this->language->account_package->package->renews : $this->language->account_package->package->expires,
                        '<strong>' . \Altum\Date::get($this->user->package_expiration_date, 2) . '</strong>'
                    ) ?>
                </p>
            <?php endif ?>
        </div>

        <div class="col">

            <div class="row mb-3">
                <div class="col-8 col-md-6">
                    <?php if($this->user->package_settings->projects_limit == -1): ?>
                        <?= $this->language->global->package_settings->unlimited_projects_limit ?>
                    <?php else: ?>
                        <?= sprintf($this->language->global->package_settings->projects_limit, '<strong>' . nr($this->user->package_settings->projects_limit) . '</strong>') ?>
                    <?php endif ?>
                </div>

                <div class="col-1">
                    <i class="fa fa-fw fa-check-circle fa-sm mr-3 text-success"></i>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-8 col-md-6">
                    <?php if($this->user->package_settings->biolinks_limit == -1): ?>
                        <?= $this->language->global->package_settings->unlimited_biolinks_limit ?>
                    <?php else: ?>
                        <?= sprintf($this->language->global->package_settings->biolinks_limit, '<strong>' . nr($this->user->package_settings->biolinks_limit) . '</strong>') ?>
                    <?php endif ?>
                </div>

                <div class="col-1">
                    <i class="fa fa-fw fa-check-circle fa-sm mr-3 text-success"></i>
                </div>
            </div>

            <?php if($this->settings->links->shortener_is_enabled): ?>
                <div class="row mb-3">
                    <div class="col-8 col-md-6">
                        <?php if($this->user->package_settings->links_limit == -1): ?>
                            <?= $this->language->global->package_settings->unlimited_links_limit ?>
                        <?php else: ?>
                            <?= sprintf($this->language->global->package_settings->links_limit, '<strong>' . nr($this->user->package_settings->links_limit) . '</strong>') ?>
                        <?php endif ?>
                    </div>

                    <div class="col-1">
                        <i class="fa fa-fw fa-check-circle fa-sm mr-3 text-success"></i>
                    </div>
                </div>
            <?php endif ?>

            <?php if($this->settings->links->domains_is_enabled): ?>
            <div class="row mb-3">
                <div class="col-8 col-md-6">
                    <?php if($this->user->package_settings->domains_limit == -1): ?>
                        <?= $this->language->global->package_settings->unlimited_domains_limit ?>
                    <?php else: ?>
                        <?= sprintf($this->language->global->package_settings->domains_limit, '<strong>' . nr($this->user->package_settings->domains_limit) . '</strong>') ?>
                    <?php endif ?>
                </div>

                <div class="col-1">
                    <i class="fa fa-fw fa-sm mr-3 <?= $this->user->package_settings->domains_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                </div>
            </div>
            <?php endif ?>

            <?php $simple_user_package_settings = require APP_PATH . 'includes/simple_user_package_settings.php' ?>
            <?php foreach($simple_user_package_settings as $row): ?>
            <div class="row mb-3">
                <div class="col-8 col-md-6">
                    <span data-toggle="tooltip" title="<?= $this->language->global->package_settings->{$row . '_help'} ?>">
                        <?= $this->language->global->package_settings->{$row} ?>
                    </span>
                </div>

                <div class="col-1">
                    <i class="fa fa-fw fa-sm <?= $this->user->package_settings->{$row} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
                </div>
            </div>
            <?php endforeach ?>

        </div>
    </div>

    <?php if($this->user->package_id != 'free' && $this->user->payment_subscription_id): ?>
        <div class="mt-8 d-flex justify-content-between">
            <div>
                <h2 class="h3"><?= $this->language->account_package->cancel->header ?></h2>
                <p class="text-muted"><?= $this->language->account_package->cancel->subheader ?></p>
            </div>

            <div class="col-auto">
                <a href="<?= url('account/cancelsubscription' . \Altum\Middlewares\Csrf::get_url_query()) ?>" class="btn btn-secondary" data-confirm="<?= $this->language->account_package->cancel->confirm_message ?>"><?= $this->language->account_package->cancel->cancel ?></a>
            </div>
        </div>
    <?php endif ?>

    <?php if($this->settings->payment->is_enabled && $this->settings->payment->codes_is_enabled): ?>
        <div class="row mt-8">
            <div class="col-12 col-md-4">
                <h2 class="h3"><?= $this->language->account_package->code->header ?></h2>

                <p class="text-muted"><?= $this->language->account_package->code->subheader ?></p>
            </div>

            <div class="col">
                <form id="code" action="<?= url('account-package/redeem_code') ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-tags text-muted mr-1"></i> <?= $this->language->account_package->code->input ?></label>
                        <input type="text" name="code" class="form-control" />
                        <div class="mt-2"><span id="code_help" class="text-muted"></span></div>
                    </div>

                    <button id="code_submit" type="submit" name="submit" class="btn btn-primary" style="display: none;"><?= $this->language->account_package->code->submit ?></button>
                </form>
            </div>
        </div>

    <?php ob_start() ?>
        <script>
            /* Disable form submission for code form */
            $('#code').on('submit', event => {
                let code = $('input[name="code"]').val();

                if(code.trim() == '') {
                    event.preventDefault();
                }
            });

            let timer = null;

            $('input[name="code"]').on('change paste keyup', event => {

                let code = $(event.currentTarget).val();
                let is_valid = false;

                clearTimeout(timer);

                if(code.trim() == '') {
                    $('#code_help').html('');
                    $(event.currentTarget).removeClass('is-invalid').removeClass('is-valid');
                    $('#code_submit').hide();

                    return;
                }

                timer = setTimeout(() => {
                    $.ajax({
                        type: 'POST',
                        url: `${url}/account-package/code`,
                        data: {code, global_token},
                        success: data => {

                            if(data.status == 'success') {
                                is_valid = true;
                            }

                            $('#code_help').html(data.message);

                            if(is_valid) {
                                $(event.currentTarget).addClass('is-valid');
                                $(event.currentTarget).removeClass('is-invalid');
                                $('#code_submit').show();
                            } else {
                                $(event.currentTarget).addClass('is-invalid');
                                $(event.currentTarget).removeClass('is-valid');
                                $('#code_submit').hide();
                            }

                        },
                        dataType: 'json'
                    });
                }, 500);

            });
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    <?php endif ?>
</section>
