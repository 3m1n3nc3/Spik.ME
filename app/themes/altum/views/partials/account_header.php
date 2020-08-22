<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex mb-5 mb-lg-0">
    <img src="<?= get_gravatar($this->user->email) ?>" class="d-none d-md-block mr-3 user-avatar" />

    <div class="d-flex flex-column">
        <h1 class="h2"><?= $this->user->name ?></h1>

        <div>
            <a href="<?= url('account-package') ?>" class="badge badge-success"><?= sprintf($this->language->account->package->header, $this->user->package->name) ?></a>

            <?php if($this->user->package_id != 'free'): ?>
                <small><?= sprintf($this->language->account->package->subheader, '<strong>' . \Altum\Date::get($this->user->package_expiration_date, 2) . '</strong>') ?></small>
            <?php endif ?>
        </div>
    </div>
</div>

<ul class="mt-5 nav nav-custom">
    <li class="nav-item">
        <a href="<?= url('account') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?>
        </a>
    </li>

    <?php if($this->settings->links->domains_is_enabled): ?>
    <li class="nav-item">
        <a href="<?= url('domains') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'domains' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= $this->language->domains->menu ?>
        </a>
    </li>
    <?php endif ?>

    <li class="nav-item">
        <a href="<?= url('account-package') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-package' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= $this->language->account_package->menu ?>
        </a>
    </li>

    <li class="nav-item">
        <a href="<?= url('account-payments') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-payments' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->account_payments->menu ?>
        </a>
    </li>

    <li class="nav-item">
        <a href="<?= url('account-logs') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-logs' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-scroll mr-1"></i> <?= $this->language->account_logs->menu ?>
        </a>
    </li>
</ul>
