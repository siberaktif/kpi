<div class="page-header">
    <h2><?= t('Delete KPI') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Are you sure you want to delete: "%s"?', $this->text->e($kpi_name)) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'KPIController',
        'remove',
        array('id' => $kpi_id, 'plugin' => 'KPI')
    ) ?>
</div>