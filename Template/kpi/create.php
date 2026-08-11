<?= $this->render('app/flash_message') ?>
<div class="page-header">
    <h2>
        <?= $this->text->e($project['name']) ?>
        &gt;
        <?= t('Create KPI') ?>
    </h2>
</div> 

<form class="js-modal-form" method="post"
    action="<?= $this->url->href(
    'KPIController',
    'save',
    [
        'plugin'=>'KPI',
        'project_id' => $project['id']
    ]
) ?>"
      autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->render('KPI:kpi/form', [
    'values' => $values,
    'errors' => $errors,
    'taskOptions'  => $taskOptions
]) ?>

</form>

