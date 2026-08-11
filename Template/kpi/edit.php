<div class="page-header">
    <h2>
        <?= t('Edit KPI') ?> &gt;
        <?= $this->text->e($values['project_name']) ?> &gt;
        <?= $this->text->e($values['title']) ?>
    </h2>
</div>

<form class="js-modal-form" method="post" action="<?= $this->url->href(
    'KPIController',
    'update',
    [
        'id' => $values['id'],
        'plugin' => 'KPI'
    ],
    'KPI'
) ?>">

    <?= $this->form->csrf() ?>

    <?= $this->render('KPI:kpi/form', [
        'values' => $values,
        'errors' => $errors,
        'taskOptions'  => $taskOptions
    ]) ?>

</form>