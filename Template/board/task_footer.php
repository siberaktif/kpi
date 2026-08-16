<?php if($kpi['id'] > 0): ?>
<span class=" text-muted">
    <?= $this->modal->medium(
        'bar-chart',
        '#' . $kpi['id'],
        'KPIController',
        'view',
        [
            'id' => $kpi['id'],
            'task_id'  => $kpi['task_id'],
            'plugin' => 'KPI',
        ],
        'KPI: ' . $kpi['title']
    ) ?>
</span>
<?php endif; ?>