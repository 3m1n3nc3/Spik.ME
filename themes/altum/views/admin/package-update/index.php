
<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= sprintf($this->language->admin_package_update->header, $data->package->name) ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/partials/admin_package_dropdown_button.php', ['id' => $data->package->package_id]) ?>
    </div>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
            <input type="hidden" name="type" value="update" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_packages->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_packages->main->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="name"><?= $this->language->admin_packages->main->name ?></label>
                        <input type="text" id="name" name="name" class="form-control form-control-lg" value="<?= $data->package->name ?>" />
                    </div>

                    <div class="form-group">
                        <label for="status"><?= $this->language->admin_packages->main->status ?></label>
                        <select id="status" name="status" class="form-control form-control-lg">
                            <option value="1" <?= $data->package->status == 1 ? 'selected="true"' : null ?>><?= $this->language->global->active ?></option>
                            <option value="0" <?= $data->package->status == 0 ? 'selected="true"' : null ?>><?= $this->language->global->disabled ?></option>
                            <option value="2" <?= $data->package->status == 2 ? 'selected="true"' : null ?>><?= $this->language->global->hidden ?></option>
                        </select>
                    </div>

                    <?php if($data->package_id == 'trial'): ?>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="days"><?= $this->language->admin_packages->main->trial->days ?></label>
                                <input type="text" id="days" name="days" class="form-control form-control-lg" value="<?= $data->package->days ?>" />
                                <div><small class="text-muted"><?= $this->language->admin_packages->main->trial->days_help ?></small></div>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if(is_numeric($data->package_id)): ?>
                        <div class="row">
                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="monthly_price"><?= $this->language->admin_packages->main->monthly_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                        <input type="text" id="monthly_price" name="monthly_price" class="form-control form-control-lg" value="<?= $data->package->monthly_price ?>" />
                                        <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->monthly_price) ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <label for="annual_price"><?= $this->language->admin_packages->main->annual_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                    <input type="text" id="annual_price" name="annual_price" class="form-control form-control-lg" value="<?= $data->package->annual_price ?>" />
                                    <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->annual_price) ?></small>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xl-4">
                                <div class="form-group">
                                    <label for="lifetime_price"><?= $this->language->admin_packages->main->lifetime_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                    <input type="text" id="lifetime_price" name="lifetime_price" class="form-control form-control-lg" value="<?= $data->package->lifetime_price ?>" />
                                    <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->lifetime_price) ?></small>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xl-6">
                                <div class="form-group">
                                    <label for="monthly_plan_id"><?= $this->language->admin_packages->main->monthly_plan_id ?></label>
                                    <input type="text" id="monthly_plan_id" name="monthly_plan_id" class="form-control form-control-lg" value="<?= $data->package->settings->monthly_plan_id ?>" />
                                    <small class="text-muted"><?= $this->language->admin_packages->main->plan_id_help ?></small>
                                </div>
                            </div>
                            
                            <div class="col-sm-12 col-xl-6">
                                <div class="form-group">
                                    <label for="annual_plan_id"><?= $this->language->admin_packages->main->annual_plan_id ?></label>
                                    <input type="text" id="annual_plan_id" name="annual_plan_id" class="form-control form-control-lg" value="<?= $data->package->settings->annual_plan_id ?>" />
                                    <small class="text-muted"><?= $this->language->admin_packages->main->plan_id_help ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                </div>
            </div>

            <div class="mt-5"></div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_packages->package->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_packages->package->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="projects_limit"><?= $this->language->admin_packages->package->projects_limit ?></label>
                        <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->package->settings->projects_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->projects_limit_help ?></small>
                    </div>

                    <div class="form-group">
                        <label for="biolinks_limit"><?= $this->language->admin_packages->package->biolinks_limit ?></label>
                        <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->package->settings->biolinks_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->biolinks_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="links_limit"><?= $this->language->admin_packages->package->links_limit ?></label>
                        <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->package->settings->links_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->links_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="biolinks_limit"><?= $this->language->admin_packages->package->domains_limit ?></label>
                        <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->package->settings->domains_limit ?>" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->domains_limit_help ?></small>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->package->settings->additional_global_domains ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="additional_global_domains"><?= $this->language->admin_packages->package->additional_global_domains ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->additional_global_domains_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_url ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="custom_url"><?= $this->language->admin_packages->package->custom_url ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_url_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->package->settings->deep_links ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="deep_links"><?= $this->language->admin_packages->package->deep_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->deep_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->package->settings->no_ads ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->package->no_ads ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->no_ads_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->package->settings->removable_branding ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->package->removable_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->removable_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_branding ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->package->custom_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_colored_links ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->package->custom_colored_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_colored_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->package->settings->statistics ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->package->statistics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->statistics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input" <?= $data->package->settings->google_analytics ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->package->google_analytics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->google_analytics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input" <?= $data->package->settings->facebook_pixel ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->package->facebook_pixel ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->facebook_pixel_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->package->settings->custom_backgrounds ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->package->custom_backgrounds ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_backgrounds_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->package->settings->verified ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->package->verified ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->verified_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input" <?= $data->package->settings->scheduling ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->package->scheduling ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->scheduling_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->package->settings->seo ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="seo"><?= $this->language->admin_packages->package->seo ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->seo_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->package->settings->utm ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="utm"><?= $this->language->admin_packages->package->utm ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->utm_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->package->settings->socials ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="socials"><?= $this->language->admin_packages->package->socials ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->socials_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->package->settings->fonts ? 'checked="true"' : null ?>>
                        <label class="custom-control-label" for="fonts"><?= $this->language->admin_packages->package->fonts ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->fonts_help ?></small></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary mb-1"><?= $this->language->global->update ?></button>
                    <button type="submit" name="submit_update_users_package_settings" class="btn btn-outline-primary mb-1"><?= $this->language->admin_package_update->update_users_package_settings->button ?></button>
                </div>
            </div>
        </form>

    </div>
</div>
