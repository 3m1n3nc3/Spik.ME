<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= $this->language->admin_user_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_user_dropdown_button.php', ['id' => $data->user->user_id]) ?>
    </div>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->main->subheader ?></p>
                </div>

                <div class="col">

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->name ?></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="<?= $data->user->name ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->email ?></label>
                        <input type="text" name="email" class="form-control form-control-lg" value="<?= $data->user->email ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->is_enabled ?></label>

                        <select class="form-control form-control-lg" name="is_enabled">
                            <option value="0" <?php if($data->user->active == 2) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_disabled ?></option>
                            <option value="1" <?php if($data->user->active == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_active ?></option>
                            <option value="0" <?php if($data->user->active == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->is_enabled_unconfirmed ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->main->type ?></label>

                        <select class="form-control form-control-lg" name="type">
                            <option value="1" <?php if($data->user->type == 1) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_admin ?></option>
                            <option value="0" <?php if($data->user->type == 0) echo 'selected' ?>><?= $this->language->admin_user_update->main->type_user ?></option>
                        </select>

                        <small class="text-muted"><?= $this->language->admin_user_update->main->type_help ?></small>
                    </div>
                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->package->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->package->header_help ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->package->package_id ?></label>

                        <select class="form-control form-control-lg" name="package_id">
                            <option value="free" <?php if($data->user->package->package_id == 'free') echo 'selected' ?>><?= $this->settings->package_free->name ?></option>
                            <option value="trial" <?php if($data->user->package->package_id == 'trial') echo 'selected' ?>><?= $this->settings->package_trial->name ?></option>
                            <option value="custom" <?php if($data->user->package->package_id == 'custom') echo 'selected' ?>><?= $this->settings->package_custom->name ?></option>

                            <?php while($row = $data->packages_result->fetch_object()): ?>
                                <option value="<?= $row->package_id ?>" <?php if($data->user->package->package_id == $row->package_id) echo 'selected' ?>><?= $row->name ?></option>
                            <?php endwhile ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->package->package_trial_done ?></label>

                        <select class="form-control form-control-lg" name="package_trial_done">
                            <option value="1" <?= $data->user->package_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->yes ?></option>
                            <option value="0" <?= !$data->user->package_trial_done ? 'selected="selected"' : null ?>><?= $this->language->global->no ?></option>
                        </select>
                    </div>

                    <div id="package_expiration_date_container" class="form-group">
                        <label><?= $this->language->admin_user_update->package->package_expiration_date ?></label>
                        <input type="text" class="form-control form-control-lg" name="package_expiration_date" autocomplete="off" value="<?= $data->user->package_expiration_date ?>">
                        <div class="invalid-feedback">
                            <?= $this->language->admin_user_update->package->package_expiration_date_invalid ?>
                        </div>
                    </div>

                    <div id="package_settings" style="display: none">
                        <div class="form-group">
                            <label for="projects_limit"><?= $this->language->admin_packages->package->projects_limit ?></label>
                            <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->package->settings->projects_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_packages->package->projects_limit_help ?></small>
                        </div>

                        <div class="form-group">
                            <label for="biolinks_limit"><?= $this->language->admin_packages->package->biolinks_limit ?></label>
                            <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->package->settings->biolinks_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_packages->package->biolinks_limit_help ?></small>
                        </div>

                        <div class="form-group" <?= !$this->settings->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                            <label for="links_limit"><?= $this->language->admin_packages->package->links_limit ?></label>
                            <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->package->settings->links_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_packages->package->links_limit_help ?></small>
                        </div>

                        <div class="form-group" <?= !$this->settings->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                            <label for="biolinks_limit"><?= $this->language->admin_packages->package->domains_limit ?></label>
                            <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->package->settings->domains_limit ?>" />
                            <small class="text-muted"><?= $this->language->admin_packages->package->domains_limit_help ?></small>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->additional_global_domains ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="additional_global_domains"><?= $this->language->admin_packages->package->additional_global_domains ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->additional_global_domains_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_url ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="custom_url"><?= $this->language->admin_packages->package->custom_url ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_url_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->deep_links ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="deep_links"><?= $this->language->admin_packages->package->deep_links ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->deep_links_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->no_ads ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->package->no_ads ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->no_ads_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->removable_branding ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->package->removable_branding ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->removable_branding_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_branding ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->package->custom_branding ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_branding_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_colored_links ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->package->custom_colored_links ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_colored_links_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->statistics ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->package->statistics ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->statistics_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->google_analytics ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->package->google_analytics ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->google_analytics_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->facebook_pixel ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->package->facebook_pixel ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->facebook_pixel_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->custom_backgrounds ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->package->custom_backgrounds ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_backgrounds_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->verified ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->package->verified ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->verified_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->scheduling ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->package->scheduling ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->scheduling_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->seo ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="seo"><?= $this->language->admin_packages->package->seo ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->seo_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->utm ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="utm"><?= $this->language->admin_packages->package->utm ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->utm_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->socials ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="socials"><?= $this->language->admin_packages->package->socials ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->socials_help ?></small></div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->user->package->settings->fonts ? 'checked="true"' : null ?>>
                            <label class="custom-control-label" for="fonts"><?= $this->language->admin_packages->package->fonts ?></label>
                            <div><small class="text-muted"><?= $this->language->admin_packages->package->fonts_help ?></small></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_user_update->change_password->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_user_update->change_password->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->change_password->new_password ?></label>
                        <input type="password" name="new_password" class="form-control form-control-lg" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_user_update->change_password->repeat_password ?></label>
                        <input type="password" name="repeat_password" class="form-control form-control-lg" />
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->update ?></button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/datepicker.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/datepicker.min.js' ?>"></script>
<script>
    let check_package_id = () => {
        let selected_package_id = $('[name="package_id"]').find(':selected').attr('value');

        if(selected_package_id == 'free') {
            $('#package_expiration_date_container').hide();
        } else {
            $('#package_expiration_date_container').show();
        }

        if(selected_package_id == 'custom') {
            $('#package_settings').show();
        } else {
            $('#package_settings').hide();
        }
    };
    check_package_id();

    /* Check for expiration date to show a warning if expired */
    let check_package_expiration_date = () => {
        let package_expiration_date = $('[name="package_expiration_date"]');

        let package_expiration_date_object = new Date(package_expiration_date.val());
        let today_date_object = new Date();

        if(package_expiration_date_object < today_date_object) {
            $(package_expiration_date).addClass('is-invalid');
        } else {
            $(package_expiration_date).removeClass('is-invalid');
        }
    };

    $('[name="package_expiration_date"]').on('change', check_package_expiration_date);
    check_package_expiration_date();

    /* Plan expiration date picker */
    $.fn.datepicker.language['altum'] = <?= json_encode(require APP_PATH . 'includes/datepicker_translations.php') ?>;
    $('[name="package_expiration_date"]').datepicker({
        classes: 'datepicker-modal',
        language: 'altum',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date(),

        onSelect: check_package_expiration_date
    });

    /* Dont show expiration date when the chosen package is the free one */
    $('[name="package_id"]').on('change', check_package_id);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
