<div class="page-header">
    <h2><?= t('Delete KPI') ?></h2>
</div>

<p><?= t('Are you sure you want to delete') ?> <strong><?= $this->text->e($kpi_name) ?></strong>?</p>

<form class="js-modal-form" method="post"
      action="<?= $this->url->href('KPIController', 'remove', [
          'id' => $kpi_id,
          'plugin' => 'KPI',
      ]) ?>">

    <?= $this->form->csrf() ?>

    <div class="form-actions">
        <button class="btn btn-red"><?= t('Delete') ?></button>
        <?= t('or') ?>
        <a href="#" class="js-modal-close"><?= t('Cancel') ?></a>
    </div>
</form>
   