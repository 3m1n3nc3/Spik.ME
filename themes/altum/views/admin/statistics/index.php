<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-lg-row justify-content-between mb-4">
    <div>
        <div class="d-flex justify-content-between">
            <h1 class="h3"><i class="fa fa-fw fa-xs fa-chart-line text-primary-900 mr-2"></i> <?= sprintf($this->language->admin_statistics->header) ?></h1>
        </div>
        <p class="text-muted"><?= $this->language->admin_statistics->subheader ?></p>
    </div>

    <div class="col-auto p-0">
        <form class="form-inline" id="datepicker_form">
            <label class="position-relative">
                <div id="datepicker_selector" class="text-muted clickable">
                    <span class="mr-1">
                        <?php if($data->date->start_date == $data->date->end_date): ?>
                            <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) ?>
                        <?php else: ?>
                            <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->date->end_date, 2, \Altum\Date::$default_timezone) ?>
                        <?php endif ?>
                    </span>
                    <i class="fa fa-fw fa-caret-down"></i>
                </div>

                <input
                        type="text"
                        id="datepicker_input"
                        data-range="true"
                        name="date_range"
                        value="<?= $data->date->input_date_range ? $data->date->input_date_range : '' ?>"
                        placeholder=""
                        autocomplete="off"
                        readonly="readonly"
                        class="custom-control-input"
                >

            </label>
        </form>
    </div>
</div>

<?php display_notifications() ?>

<div class="row">
    <div class="mb-5 mb-xl-0 col-12 col-xl-3">
        <div class="nav flex-column nav-pills">
            <a class="nav-link <?= $data->type == 'growth' ? 'active' : null ?>" href="<?= url('admin/statistics/growth') ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= $this->language->admin_statistics->growth->menu ?></a>
            <?php if(in_array($this->settings->license->type, ['SPECIAL','Extended License'])): ?>
            <a class="nav-link <?= $data->type == 'payments' ? 'active' : null ?>" href="<?= url('admin/statistics/payments') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->admin_statistics->payments->menu ?></a>
            <?php endif ?>
            <a class="nav-link <?= $data->type == 'links' ? 'active' : null ?>" href="<?= url('admin/statistics/links') ?>"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= $this->language->admin_statistics->links->menu ?></a>
        </div>
    </div>

    <div class="col-12 col-xl-9">

        <?php

        /* Load the proper type view */
        $partial = require THEME_PATH . 'views/admin/statistics/partials/' . $data->type . '.php';

        echo $partial->html;

        ?>

    </div>
</div>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/datepicker.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/datepicker.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>

<script>
    /* Datepicker */
    $.fn.datepicker.language['altum'] = <?= json_encode(require APP_PATH . 'includes/datepicker_translations.php') ?>;
    let datepicker = $('#datepicker_input').datepicker({
        language: 'altum',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        maxDate: new Date(),

        onSelect: (formatted_date, date) => {

            if(date.length > 1) {
                let [ start_date, end_date ] = formatted_date.split(',');

                if(typeof end_date == 'undefined') {
                    end_date = start_date
                }

                /* Redirect */
                redirect(`admin/statistics/<?= $data->type ?>?start_date=${start_date}&end_date=${end_date}`);
            }
        }
    });

    /* Default chart options */
    let chart_options = {
        animation: {
            duration: 0
        },
        hover: {
            animationDuration: 0
        },
        responsiveAnimationDuration: 0,
        elements: {
            line: {
                tension: 0
            }
        },
        tooltips: {
            mode: 'index',
            intersect: false
        },
        title: {
            text: '',
            display: true
        },
        scales: {
            yAxes: [{
                gridLines: {
                    display: false
                },
                ticks: {
                    userCallback: (value, index, values) => {
                        if (Math.floor(value) === value) {
                            return nr(value);
                        }
                    },
                }
            }],
            xAxes: [{
                gridLines: {
                    display: false
                }
            }]
        },
        responsive: true,
        maintainAspectRatio: false
    };

    let css = window.getComputedStyle(document.body)
</script>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
<?= $partial->javascript ?>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
