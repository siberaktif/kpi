<div class="page-header">
    <h2>
        <?php echo t('Edit KPI') ?> &gt;
        <?php echo $this->text->e($values['project_name']) ?> &gt;
        <?php echo $this->text->e($values['title']) ?>
    </h2>
</div>

<form class="js-modal-form" method="post" action="<?php echo $this->url->href(
    'KPIController',
    'update',
    [
        'id'     => $values['id'],
        'plugin' => 'KPI',
    ],
    'KPI'
) ?>">

    <?php echo $this->form->csrf() ?>

    <?php echo $this->render('KPI:kpi/form', [
    'values'       => $values,
    'errors'       => $errors,
    'taskId'       => $taskId,
    'taskPoint'    => $taskPoint,
    'taskOptions'  => $taskOptions,
]) ?>

</form>