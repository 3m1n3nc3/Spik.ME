<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-link fa-xs text-muted"></i> <?= $this->language->admin_statistics->links->track_links->header ?></h2>
        <p class="text-muted"><?= $this->language->admin_statistics->links->track_links->subheader ?></p>

        <div class="chart-container">
            <canvas id="track_links"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let track_links_color = css.getPropertyValue('--gray-500');

    /* Display chart */
    new Chart(document.getElementById('track_links').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $data->track_links_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($this->language->admin_statistics->links->track_links->chart_track_links) ?>,
                data: <?= $data->track_links_chart['track_links'] ?? '[]' ?>,
                backgroundColor: track_links_color,
                borderColor: track_links_color,
                fill: false
            }]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
