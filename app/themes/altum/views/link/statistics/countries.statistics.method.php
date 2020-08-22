<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->country_code ?></h3>
        <p class="text-muted mb-3"><?= $this->language->link->statistics->country_code_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-truncate">
                        <?php if(!$row->country_code): ?>
                            <span><?= $this->language->link->statistics->country_code_unknown ?></span>
                        <?php else: ?>
                            <img src="https://www.countryflags.io/<?= $row->country_code ?>/flat/16.png" class="img-fluid mr-1" />
                            <span class="align-middle"><?= get_country_from_country_code($row->country_code) ?></span>
                        <?php endif ?>
                    </div>

                    <div>
                        <span class="badge badge-pill badge-primary"><?= nr($row->total) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
