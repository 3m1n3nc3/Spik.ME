<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= $this->language->admin_packages->header ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/package-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_packages->create ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_packages->table->name ?></th>
            <th><?= $this->language->admin_packages->table->monthly_price ?></th>
            <th><?= $this->language->admin_packages->table->annual_price ?></th>
            <th><?= $this->language->admin_packages->table->users ?></th>
            <th><?= $this->language->admin_packages->table->status ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><a href="<?= url('admin/package-update/free') ?>"><?= $this->settings->package_free->name ?></a></td>
            <td>-</td>
            <td>-</td>
            <td><i class="fa fa-fw fa-users text-muted"></i> <?= $this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `package_id` = 'free'")->fetch_object()->total ?? 0 ?></td>
            <td>
                <?php if($this->settings->package_free->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                <?php elseif($this->settings->package_free->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_package_dropdown_button.php', ['id' => 'free']) ?></td>
        </tr>

        <tr>
            <td><a href="<?= url('admin/package-update/trial') ?>"><?= $this->settings->package_trial->name ?></a></td>
            <td>-</td>
            <td>-</td>
            <td><i class="fa fa-fw fa-users text-muted"></i> <?= $this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `package_id` = 'trial'")->fetch_object()->total ?? 0 ?></td>
            <td>
                <?php if($this->settings->package_trial->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                <?php elseif($this->settings->package_trial->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_package_dropdown_button.php', ['id' => 'trial']) ?></td>
        </tr>

        <tr>
            <td>
                <?= $this->settings->package_custom->name ?>
                <span data-toggle="tooltip" title="<?= $this->language->admin_packages->table->custom_help ?>"><i class="fa fa-fw fa-info-circle text-muted"></i></span>
            </td>
            <td>-</td>
            <td>-</td>
            <td><i class="fa fa-fw fa-users text-muted"></i> <?= $this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `package_id` = 'custom'")->fetch_object()->total ?? 0 ?></td>
            <td><span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span></td>
            <td></td>
        </tr>

        <?php while($row = $data->packages_result->fetch_object()): ?>

            <tr>
                <td><a href="<?= url('admin/package-update/' . $row->package_id) ?>"><?= $row->name ?></a></td>
                <td><?= $row->monthly_price . ' ' . $this->settings->payment->currency ?></td>
                <td><?= $row->annual_price . ' ' . $this->settings->payment->currency ?></td>
                <td><i class="fa fa-fw fa-users text-muted"></i> <?= $this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `package_id` = '{$row->package_id}'")->fetch_object()->total ?? 0 ?></td>
                <td>
                    <?php if($row->status == 0): ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                    <?php elseif($row->status == 1): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                    <?php endif ?>
                </td>
                <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_package_dropdown_button.php', ['id' => $row->package_id]) ?></td>
            </tr>

        <?php endwhile ?>
        </tbody>
    </table>
</div>
