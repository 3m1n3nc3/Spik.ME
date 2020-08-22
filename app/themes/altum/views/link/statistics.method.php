<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-3">
    <h2 class="h4 mr-3"><?= $this->language->link->statistics->header ?></h2>

    <div>
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
                        data-min="<?= (new \DateTime($data->link->date))->format('Y-m-d') ?>"
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

<?php if(!count($data->logs)): ?>

    <div class="d-flex flex-column align-items-center justify-content-center">
        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->no_data ?>" />
        <h2 class="h4 text-muted"><?= $this->language->link->statistics->no_data ?></h2>
    </div>

<?php elseif(!$this->user->package_settings->statistics): ?>

    <?php if($this->settings->payment->is_enabled): ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/unlock.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->missing_statistics_package ?>" />
            <h2 class="h4"><a href="<?= url('package') ?>"><?= $this->language->global->info_message->unlock_feature ?></a></h2>
            <span class="text-muted"><?= $this->language->link->statistics->missing_statistics_package ?></span>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/unlock.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->link->statistics->missing_statistics_package ?>" />
            <h2 class="h4"><a href="<?= url('package') ?>"><?= $this->language->global->info_message->locked_feature ?></a></h2>
            <span class="text-muted"><?= $this->language->link->statistics->missing_statistics_package ?></span>
        </div>
    <?php endif ?>

<?php else: ?>

    <div class="chart-container mb-5">
        <canvas id="clicks_chart"></canvas>
    </div>

    <ul class="nav nav-pills flex-column flex-lg-row mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'lastactivity' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/lastactivity?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-list mr-1"></i>
                <?= $this->language->link->statistics->lastactivity ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'referrers' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/referrers?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-random mr-1"></i>
                <?= $this->language->link->statistics->referrer ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'countries' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/countries?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-globe mr-1"></i>
                <?= $this->language->link->statistics->country_code ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'devices' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/devices?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-laptop mr-1"></i>
                <?= $this->language->link->statistics->device_type ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browsers' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/browsers?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-window-restore mr-1"></i>
                <?= $this->language->link->statistics->browser_name ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browserlanguages' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/browserlanguages?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-language mr-1"></i>
                <?= $this->language->link->statistics->browser_language ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'operatingsystems' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/statistics/operatingsystems?start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-server mr-1"></i>
                <?= $this->language->link->statistics->os_name ?>
            </a>
        </li>
    </ul>

    <?= $this->views['statistics.method'] ?>

<?php endif ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/datepicker.min.js' ?>"></script>

<script>
    /* Datepicker */
    $.fn.datepicker.language['altum'] = <?= json_encode(require APP_PATH . 'includes/datepicker_translations.php') ?>;
    let datepicker = $('#datepicker_input').datepicker({
        language: 'altum',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date($('#datepicker_input').data('min')),
        maxDate: new Date(),

        onSelect: (formatted_date, date) => {

            if(date.length > 1) {
                let [ start_date, end_date ] = formatted_date.split(',');

                if(typeof end_date == 'undefined') {
                    end_date = start_date
                }

                /* Redirect */
                redirect(`${$('#base_controller_url').val()}/statistics/<?= $data->type ?>?start_date=${start_date}&end_date=${end_date}`, true);
            }
        }
    });

    /* Charts */
    <?php if(count($data->logs)): ?>
    /* Charts */
    Chart.defaults.global.elements.line.borderWidth = 4;
    Chart.defaults.global.elements.point.radius = 3;
    Chart.defaults.global.elements.point.borderWidth = 7;

    let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

    let gradient = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
    gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

    let gradient_white = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient_white.addColorStop(0, 'rgba(255, 255, 255, 0.6)');
    gradient_white.addColorStop(1, 'rgba(255, 255, 255, 0.05)');

    new Chart(clicks_chart, {
        type: 'line',
        data: {
            labels: <?= $data->logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->link->statistics->impressions) ?>,
                data: <?= $data->logs_chart['impressions'] ?? '[]' ?>,
                backgroundColor: gradient,
                borderColor: '#38B2AC',
                fill: true
            },
            {
                label: <?= json_encode($this->language->link->statistics->uniques) ?>,
                data: <?= $data->logs_chart['uniques'] ?? '[]' ?>,
                backgroundColor: gradient_white,
                borderColor: '#ebebeb',
                fill: true
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${nr(value)} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                display: true,
                text: <?= json_encode($this->language->link->statistics->clicks_chart) ?>
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
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
                        }
                    },
                    min: 0
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });

    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
