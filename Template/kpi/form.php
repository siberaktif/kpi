<div class="kb-form">
    <div class="kb-form-body kb-row">
        <div class="kb-col kb-col-6">
            <input type="hidden"
                id="project_id"
                name="project_id"
                value="<?= $this->text->e($values['project_id']) ?>">

            <div>
                <?= $this->form->label(t('Title'), 'title') ?>
                <?= $this->form->text('title', $values, $errors) ?>
            </div>

            <div>
                <?= $this->form->label(t('Description'), 'description') ?>
                <?= $this->form->textEditor(
                    'description',
                    $values,
                    $errors
                ) ?>
            </div>

            <div>
                <?= $this->form->label(t('Output'), 'output') ?>
                <?= $this->form->textEditor(
                    'output',
                    $values,
                    $errors
                ) ?>
            </div>
        </div>

        <div class="kb-col kb-col-6">
            <div class="kb-row">
                <div class="kb-col kb-col-6">
                    <?= $this->form->label(t('Type'), 'type') ?>
                    <?= $this->form->select(
                        'type',
                        [
                            'MAJOR' => t('MAJOR'),
                            'MINOR' => t('MINOR'),
                        ],
                        $values,
                        $errors
                    ) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?= $this->form->label(t('UOM'), 'target_unit') ?>
                    <?= $this->form->text('target_unit', $values, $errors) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?= $this->form->label(t('Target'), 'target') ?>
                    <?= $this->form->text('target', $values, $errors) ?>
                </div>
                
                <div class="kb-col kb-col-6">
                    <?= $this->form->label(t('Actual'), 'actual') ?>
                    <?= $this->form->text('actual', $values, $errors) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?= $this->form->label(t('Status'), 'status') ?>
                    <?= $this->form->select(
                        'status',
                        [
                            'PLANNED' => t('PLANNED'),
                            'SCHEDULED' => t('SCHEDULED'),
                            'PENDING' => t('PENDING'),
                            'ONGOING' => t('ONGOING'),
                            'DONE' => t('DONE'),
                        ],
                        $values,
                        $errors
                    ) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?php $values['task_id'] = $taskId ?? 0;?>
                    <?= $this->form->label(t('Assign Task'), 'task_id') ?>
                    <?= $this->form->select('task_id', $taskOptions, $values, $errors) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?php $values['task_point'] = $taskPoint ?? 0;?>                    
                    <?= $this->form->label(t('KPI Point'), 'task_point') ?>
                    <?= $this->form->text('task_point', $values, $errors) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?= $this->form->date(t('Timeline Started'), 'timeline_started', $values, $errors) ?>
                </div>

                <div class="kb-col kb-col-6">
                    <?= $this->form->date(t('Timeline Completed'), 'timeline_completed', $values, $errors) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="kb-form-footer">
         <?= $this->modal->submitButtons() ?>
    </div>
</div>