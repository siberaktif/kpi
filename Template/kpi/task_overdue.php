<?= $this->projectHeader->render($project,'TaskController','task_overdue',false,'KPI');?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><?= t('Overdue Tasks') ?> (<?= count($tasks) ?>)</h2>
        </div>
    </div>

    <?php if (empty($tasks)): ?>

        <div class="alert alert-info">
            <?= t('No overdue tasks found.') ?>
        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-header text-white">
                <?= t('Task List') ?>
            </div>

            <div class="kb-table-container">
                <table class="kb-table">
                    <thead class="table-light">
                    <tr>
                        <th><?= t('Title') ?></th>
                        <th width="150"><?= t('Assignee') ?></th>
                        <th width="200"><?= t('Start Date') ?></th>
                        <th width="200"><?= t('Due Date') ?></th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($tasks as $task): ?>

                        <tr>
                             <td>
                                <?php if (!empty($task['description'])): ?>
                                <?= $this->modal->small(
                                    'file',
                                    '',
                                    'BoardTooltipController',
                                    'description',
                                    [
                                        'task_id' => $task['id'],
                                        'project_id' => $task['project_id'],
                                    ]
                                ) ?>
                                <?php else: ?>
                                    <i class="fa fa-file ms-1 me-1" style="color: #6c757da0"></i>
                                <?php endif; ?>

                                <div class="dropdown">
                                    <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                        <strong>#<?= $this->text->e($task['id']) ?>
                                            <i class="fa fa-caret-down"></i>
                                        </strong>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <?= $this->modal->large(
                                                'edit',
                                                t('Edit the task'),
                                                'TaskModificationController',
                                                'edit',
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
                                            <?= $this->modal->medium(
                                                'trash',
                                                t('Remove'),
                                                'taskSuppressionController',
                                                'confirm',
                                                [
                                                    'task_id' => $task['id']
                                                ],
                                                false,
                                                '',
                                                true
                                            ) ?>
                                        </li>
                                    </ul>
                                </div>
                                <?= $this->url->link($this->text->e($task['title']),'TaskViewController','show',
                                [
                                    'task_id' => $task['id'],
                                    'project_id' => $project['id']
                                ],
                                'task-board-title') ?>
                            </td>

                            <td>
                                <?= $task['owner_id'] ?: '-' ?>
                            </td>

                            <td>
                                <?= $task['date_started'] > 0
                                    ? date('l, F j, Y', $task['date_started'])
                                    : '-' ?>
                            </td>

                            <td>
                                <?= $task['date_due'] > 0
                                    ? date('l, F j, Y', $task['date_due'])
                                    : '-' ?>
                            </td>

                        </tr>

                    <?php endforeach ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endif; ?>
</div>