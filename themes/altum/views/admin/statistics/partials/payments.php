<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-dollar-sign fa-xs text-muted"></i> <?= $this->language->admin_statistics->payments->header ?></h2>

        <div class="chart-container">
            <canvas id="payments"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let total_payments_color = css.getPropertyValue('--gray-500');
    let total_amount_color = css.getPropertyValue('--primary');

    /* Display chart */
    new Chart(document.getElementById('payments').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $data->payments_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode($this->language->admin_statistics->payments->chart_total_payments) ?>,
                    data: <?= $data->payments_chart['total_payments'] ?? '[]' ?>,
                    backgroundColor: total_payments_color,
                    borderColor: total_payments_color,
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->admin_statistics->payments->chart_total_amount) ?>,
                    data: <?= $data->payments_chart['total_amount'] ?? '[]' ?>,
                    backgroundColor: total_amount_color,
                    borderColor: total_amount_color,
                    fill: false
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
