<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->lastactivity ?></h3>
        <p class="text-muted mb-4"><?= $this->language->link->statistics->lastactivity_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <?php
            $icon = new \Jdenticon\Identicon([
                'value' => $row->dynamic_id,
                'size' => 50,
                'style' => [
                    'hues' => [165, 168, 170, 173, 175, 177, 180, 183],
                    'backgroundColor' => '#86444400',
                    'colorLightness' => [0.41, 0.80],
                    'grayscaleLightness' => [0.30, 0.70],
                    'colorSaturation' => 0.85,
                    'grayscaleSaturation' => 0.40,
                ]
            ]);
            $row->icon = $icon->getImageDataUri();
            ?>

            <div class="row mb-4">
                <div class="col-2 col-md-1">
                    <img src="<?= $row->icon ?>" class="mr-3" alt="" />
                </div>

                <div class="col-10 col-md-5">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center mr-3">
                            <?php if($row->country_code): ?>
                                <img src="https://www.countryflags.io/<?= $row->country_code ?>/flat/16.png" class="img-fluid mr-1" />
                                <?= get_country_from_country_code($row->country_code) ?>
                            <?php else: ?>
                                <i class="fa fa-fw fa-sm fa-globe mr-1"></i>
                                <?= $this->language->link->statistics->country_code_unknown ?>
                            <?php endif ?>
                        </div>

                        <small class="text-muted"><?= \Altum\Date::get_timeago($row->last_date) ?></small>
                    </div>

                    <div class="text-truncate">
                        <?php if($row->referrer): ?>
                            <img src="https://www.google.com/s2/favicons?domain=<?= $row->referrer ?>" class="img-fluid mr-1" />
                            <small><a href="<?= $row->referrer ?>" title="<?= $row->referrer ?>" class="text-muted align-middle"><?= $row->referrer ?></a></small>
                        <?php else: ?>
                            <small class="text-muted"><?= $this->language->link->statistics->referrer_direct ?></small>
                        <?php endif ?>
                    </div>
                </div>

                <div class="d-none d-md-block col-3">
                    <div class="d-flex align-items-center">
                        <?php if($row->device_type): ?>
                            <i class="fa fa-fw fa-sm fa-<?= $row->device_type ?> mr-1"></i>
                            <?= $this->language->link->statistics->{'device_type_' . $row->device_type} ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->device_type_unknown ?>
                        <?php endif ?>
                    </div>

                    <div class="text-muted">
                        <?php if($row->os_name): ?>
                            <?= $row->os_name ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->os_name_unknown ?>
                        <?php endif ?>
                    </div>
                </div>

                <div class="d-none d-md-block col-3">
                    <div class="d-flex">
                        <?php if($row->browser_name): ?>
                            <?= $row->browser_name ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->browser_name_unknown ?>
                        <?php endif ?>
                    </div>

                    <div class="text-muted">
                        <?php if($row->browser_language): ?>
                            <?= $row->browser_language ?>
                        <?php else: ?>
                            <?= $this->language->link->statistics->browser_language_unknown ?>
                        <?php endif ?>
                    </div>
                </div>

            </div>
        <?php endforeach ?>
    </div>
</div>
