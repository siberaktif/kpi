<?= $this->render('app/flash_message') ?>
<?= $this->projectHeader->render($project,'KPIController','project',false,'KPI');?> 

<div class="container">
    <div class="container mt-2 p-3 justify-between align-center d-flex">
        <div class="btn bg-primary">
            <?= $this->modal->large(
                'plus',
                t('Add KPI'),
                'KPIController', 
                'create',
                [
                    'tasks' => $tasks,
                    'project_id' => $project['id'],
                    'plugin' => 'KPI'
                ]) ?>
        </div>
        <Strong><?= t('Key Performance Indicators') ?></Strong>
    </div>

    <div class="container">
        <div class="pb-2">
            <strong>MAJOR KPIs</strong>
        </div>
        <table class="kb-table kb-table-striped">
            <thead>
                <tr>
                    <th><?= t('Activities / Task') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Assign Task') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('UOM') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Target') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Actual') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Timeline') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Status') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($kpis as $kpi): ?>
                <?php if ($kpi['type'] !== 'MAJOR') continue; ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                <strong>#<?= $this->text->e($kpi['id']) ?>
                                    <i class="fa fa-caret-down"></i>
                                </strong>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->modal->large(
                                        'edit',
                                        t('Edit KPI'),
                                        'KPIController',
                                        'edit',
                                        [
                                            'id' => $kpi['id'],
                                            'plugin' => 'KPI'
                                        ],
                                        false,
                                        '',
                                        true
                                    ) ?>
                                </li>

                                <li>
                                    <?= $this->modal->medium(
                                        'comment',
                                        t('Add a comment'),
                                        'CommentController',
                                        'create',
                                        [
                                            'task_id' => $task['id'],
                                            'project_id' => $project['id'],
                                        ],
                                        false,
                                        '',
                                        true
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->modal->small(
                                        'trash',
                                        t('Remove'),
                                        'KPIController',
                                        'confirm',
                                        [
                                            'kpi_id' => $kpi['id'],
                                            'kpi_name' => $kpi['name'],
                                            'plugin' => 'KPI'
                                        ],
                                        false,
                                        '',
                                        true
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                        <?= $this->text->e($kpi['title']) ?>
                    </td>

                    <td class="kb-text-center">
                        <?php if($kpi['task_id'] > 0): ?>

                            <div class="dropdown">

                                <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                    <strong>
                                        #<?= $kpi['task_id'] ?>
                                        <i class="fa fa-caret-down"></i>
                                    </strong>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <?= $this->modal->large(
                                            'arrow-right',
                                            t($kpi['task_name']),
                                            'TaskController',
                                            'taskAssign',
                                            [
                                                'id' => $kpi['task_id'],
                                                'project_id' => $project['id'],
                                                'plugin' => 'KPI'
                                            ],
                                            false,
                                            '',
                                            true
                                        ) ?>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="click-truncate">
                        <?= $kpi['target_unit'] !== '' ? $kpi['target_unit'] : '-' ?>
                    </td>

                    <td class="kb-text-center">
                        <?= number_format($kpi['target']) ?>
                    </td>

                    <td class="kb-text-center">
                        <?= number_format($kpi['actual']) ?>
                    </td>

                    <td class="kb-text-center">
                        <?php if($kpi['timeline_start'] && $kpi['timeline_complete']): ?>
                        <?= date('M', strtotime($kpi['timeline_start'])). ' - ' . date('M', strtotime($kpi['timeline_complete'])) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <?php if ($kpi['status'] === 'DONE'): ?>
                        <td class="bg-success">
                            <span><?= t('DONE') ?></span>
                        </td>
                    <?php elseif ($kpi['status'] === 'ONGOING'): ?>
                        <td class="bg-warning">
                            <span><?= t('ONGOING') ?></span>
                        </td>
                    <?php elseif ($kpi['status'] === 'SCHEDULED'): ?>
                        <td class="bg-info">
                            <span><?= t('SCHEDULED') ?></span>
                        </td>
                    <?php elseif ($kpi['status'] === 'PLANNED'): ?>
                        <td class="bg-secondary">
                            <span><?= t('PLANNED') ?></span>
                        </td>
                    <?php else: ?>
                        <td class="bg-danger">
                            <span><?= t('PENDING') ?></span>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <div class="pb-2">
            <strong>MINOR KPIs</strong>
        </div>
        <table class="kb-table kb-table-striped">
            <thead>
                <tr>
                    <th><?= t('Activities / Task') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Assign Task') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('UOM') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Target') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Actual') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Timeline') ?></th>
                    <th class="kb-text-center" style="width: 100px;"><?= t('Status') ?></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($kpis as $kpi): ?>
                <?php if ($kpi['type'] !== 'MINOR') continue; ?>
                    <tr>
                        <td>
                            <div class="dropdown">
                                <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                    <strong>#<?= $this->text->e($kpi['id']) ?>
                                        <i class="fa fa-caret-down"></i>
                                    </strong>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <?= $this->modal->large(
                                            'edit',
                                            t('Edit KPI'),
                                            'KPIController',
                                            'edit',
                                            [
                                                'id' => $kpi['id'],
                                                'plugin' => 'KPI'
                                            ],
                                            false,
                                            '',
                                            true
                                        ) ?>
                                    </li>

                                    <li>
                                        <?= $this->modal->medium(
                                            'comment',
                                            t('Add a comment'),
                                            'CommentController',
                                            'create',
                                            [
                                                'task_id' => $task['id'],
                                                'project_id' => $project['id'],
                                            ],
                                            false,
                                            '',
                                            true
                                        ) ?>
                                    </li>
                                    <li>
                                        <?= $this->modal->small(
                                            'trash',
                                            t('Remove'),
                                            'KPIController',
                                            'confirm',
                                            [
                                                'kpi_id' => $kpi['id'],
                                                'kpi_name' => $kpi['name'],
                                                'plugin' => 'KPI'
                                            ],
                                            false,
                                            '',
                                            true
                                        ) ?>
                                    </li>
                                </ul>
                            </div>
                            <?= $this->text->e($kpi['title']) ?>
                        </td>

                        <td class="kb-text-center">
                            <?php if($kpi['task_id'] > 0): ?>

                                <div class="dropdown">

                                    <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                        <strong>
                                            #<?= $kpi['task_id'] ?>
                                            <i class="fa fa-caret-down"></i>
                                        </strong>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <?= $this->modal->large(
                                                'arrow-right',
                                                t($kpi['task_name']),
                                                'TaskController',
                                                'taskAssign',
                                                [
                                                    'id' => $kpi['task_id'],
                                                    'project_id' => $project['id'],
                                                    'plugin' => 'KPI'
                                                ],
                                                false,
                                                '',
                                                true
                                            ) ?>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td class="click-truncate">
                            <?= $kpi['target_unit'] !== '' ? $kpi['target_unit'] : '-' ?>
                        </td>

                        <td class="kb-text-center">
                            <?= number_format($kpi['target']) ?>
                        </td>

                        <td class="kb-text-center">
                            <?= number_format($kpi['actual']) ?>
                        </td>

                        <td class="kb-text-center">
                            <?php if($kpi['timeline_start'] && $kpi['timeline_complete']): ?>
                            <?= date('M', strtotime($kpi['timeline_start'])). ' - ' . date('M', strtotime($kpi['timeline_complete'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <?php if ($kpi['status'] === 'DONE'): ?>
                            <td class="bg-success">
                                <span><?= t('DONE') ?></span>
                            </td>
                        <?php elseif ($kpi['status'] === 'ONGOING'): ?>
                            <td class="bg-warning">
                                <span><?= t('ONGOING') ?></span>
                            </td>
                        <?php elseif ($kpi['status'] === 'SCHEDULED'): ?>
                            <td class="bg-info">
                                <span><?= t('SCHEDULED') ?></span>
                            </td>
                        <?php elseif ($kpi['status'] === 'PLANNED'): ?>
                            <td class="bg-secondary">
                                <span><?= t('PLANNED') ?></span>
                            </td>
                        <?php else: ?>
                            <td class="bg-danger">
                                <span><?= t('PENDING') ?></span>
                            </td>
                        <?php endif ?>
                    </tr>
            <?php endforeach ?>
            </tbody>

        </table>
    </div>
</div>