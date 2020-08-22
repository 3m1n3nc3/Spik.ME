<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mr-3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= $this->language->admin_package_create->header ?></h1>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row">
                <div class="col-12 col-md-4">
                    <h2 class="h4"><?= $this->language->admin_packages->main->header ?></h2>
                    <p class="text-muted"><?= $this->language->admin_packages->main->subheader ?></p>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="name"><?= $this->language->admin_packages->main->name ?></label>
                        <input type="text" id="name" name="name" class="form-control form-control-lg" />
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_packages->main->status ?></label>
                        <select id="status" name="status" class="form-control form-control-lg">
                            <option value="1"><?= $this->language->global->active ?></option>
                            <option value="0"><?= $this->language->global->disabled ?></option>
                            <option value="2"><?= $this->language->global->hidden ?></option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-xl-4">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="monthly_price"><?= $this->language->admin_packages->main->monthly_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                    <input type="text" id="monthly_price" name="monthly_price" class="form-control form-control-lg" />
                                    <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->monthly_price) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-xl-4">
                            <div class="form-group">
                                <label for="annual_price"><?= $this->language->admin_packages->main->annual_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                <input type="text" id="annual_price" name="annual_price" class="form-control form-control-lg" />
                                <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->annual_price) ?></small>
                            </div>
                        </div>

                        <div class="col-sm-12 col-xl-4">
                            <div class="form-group">
                                <label for="lifetime_price"><?= $this->language->admin_packages->main->lifetime_price ?> <small class="text-muted"><?= $this->settings->payment->currency ?></small></label>
                                <input type="text" id="lifetime_price" name="lifetime_price" class="form-control form-control-lg" />
                                <small class="text-muted"><?= sprintf($this->language->admin_packages->main->price_help, $this->language->admin_packages->main->lifetime_price) ?></small>
                            </div>
                        </div>

                        <div class="col-sm-12 col-xl-6">
                            <div class="form-group">
                                <label for="monthly_plan_id"><?= $this->language->admin_packages->main->monthly_plan_id ?></label>
                                <input type="text" id="monthly_plan_id" name="monthly_plan_id" class="form-control form-control-lg" />
                                <small class="text-muted"><?= $this->language->admin_packages->main->plan_id_help ?></small>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xl-6">
                            <div class="form-group">
                                <label for="annual_plan_id"><?= $this->language->admin_packages->main->annual_plan_id ?></label>
                                <input type="text" id="annual_plan_id" name="annual_plan_id" class="form-control form-control-lg" />
                                <small class="text-muted"><?= $this->language->admin_packages->main->plan_id_help ?></small>
                            </div>
                        </div>
                    </div>

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
                        <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->projects_limit_help ?></small>
                    </div>

                    <div class="form-group">
                        <label for="biolinks_limit"><?= $this->language->admin_packages->package->biolinks_limit ?></label>
                        <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->biolinks_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="links_limit"><?= $this->language->admin_packages->package->links_limit ?></label>
                        <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->links_limit_help ?></small>
                    </div>

                    <div class="form-group" <?= !$this->settings->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                        <label for="biolinks_limit"><?= $this->language->admin_packages->package->domains_limit ?></label>
                        <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" />
                        <small class="text-muted"><?= $this->language->admin_packages->package->domains_limit_help ?></small>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="additional_global_domains"><?= $this->language->admin_packages->package->additional_global_domains ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->additional_global_domains_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="custom_url"><?= $this->language->admin_packages->package->custom_url ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_url_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="deep_links"><?= $this->language->admin_packages->package->deep_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->deep_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="no_ads"><?= $this->language->admin_packages->package->no_ads ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->no_ads_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="removable_branding"><?= $this->language->admin_packages->package->removable_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->removable_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="custom_branding"><?= $this->language->admin_packages->package->custom_branding ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_branding_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="custom_colored_links"><?= $this->language->admin_packages->package->custom_colored_links ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_colored_links_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="statistics" name="statistics" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="statistics"><?= $this->language->admin_packages->package->statistics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->statistics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="google_analytics" name="google_analytics" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="google_analytics"><?= $this->language->admin_packages->package->google_analytics ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->google_analytics_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="facebook_pixel" name="facebook_pixel" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="facebook_pixel"><?= $this->language->admin_packages->package->facebook_pixel ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->facebook_pixel_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="custom_backgrounds"><?= $this->language->admin_packages->package->custom_backgrounds ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->custom_backgrounds_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="verified" name="verified" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="verified"><?= $this->language->admin_packages->package->verified ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->verified_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="scheduling" name="scheduling" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="scheduling"><?= $this->language->admin_packages->package->scheduling ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->scheduling_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="seo" name="seo" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="seo"><?= $this->language->admin_packages->package->seo ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->seo_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="utm" name="utm" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="utm"><?= $this->language->admin_packages->package->utm ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->utm_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="socials" name="socials" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="socials"><?= $this->language->admin_packages->package->socials ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->socials_help ?></small></div>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input id="fonts" name="fonts" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="fonts"><?= $this->language->admin_packages->package->fonts ?></label>
                        <div><small class="text-muted"><?= $this->language->admin_packages->package->fonts_help ?></small></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-md-4"></div>

                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->create ?></button>
                </div>
            </div>
        </form>

    </div>
</div>
