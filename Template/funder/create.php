<div class="page-header">
    <h2><?= t('Add Project and Funder Informations') ?></h2>
</div> 

<form class="js-modal-form" method="post" action="<?= $this->url->href('FunderController','save',['plugin'=>'KPI']) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <div class="kb-form">
        <div class="kb-form-body kb-row">
            <div class="kb-col kb-col-6">
                <div>
                    <?= $this->form->label(t('Project Name'), 'project_name') ?>
                    <?= $this->form->text('project_name', $values, $errors) ?>
                </div>

                <div>
                    <?= $this->form->label(t('Project Short Name'), 'project_alias') ?>
                    <?= $this->form->text('project_alias', $values, $errors) ?>
                </div>

                <div>
                    <?= $this->form->label(t('Funder Name'), 'funder_name') ?>
                    <?= $this->form->text('funder_name', $values, $errors) ?>
                </div>

                <div>
                    <?= $this->form->label(t('Description'), 'description') ?>
                    <?= $this->form->textEditor(
                        'description',
                        $values,
                        $errors
                    ) ?>
                </div>
            </div>

            <div class="kb-col kb-col-6">
                <div class="kb-row">
                     <div class="kb-col kb-col-3">
                        <?= $this->form->label(t('Year Duration'), 'year_duration') ?>
                        <?= $this->form->text('year_duration', $values, $errors) ?>
                    </div>
                    <div class="kb-col kb-col-3">
                        <?= $this->form->date(t('Date Started'), 'date_started', $values, $errors) ?>
                    </div>
                    <div class="kb-col kb-col-3">
                        <?= $this->form->date(t('Date Completed'), 'date_completed', $values, $errors) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="kb-form-footer">
         <?= $this->modal->submitButtons() ?>
    </div>
</div>

</form>