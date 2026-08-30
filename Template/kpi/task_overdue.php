<?php
$tasks   = $tasks ?? [];
$project = $project ?? [];
$projectId = isset($project['id']) ? $project['id'] : 0;
?>
<?= $this->projectHeader->render($project,'TaskController','task_overdue',false,'KPI');?>
<?php
$sortUrl = function ($field) use ($projectId, $sort, $direction) {
    return $this->url->href(
        'TaskController',
        'task_overdue',
        [
            'project_id' => $projectId,
            'plugin' => 'KPI',
            'sort' => $field,
            'direction' => (
                $sort === $field && $direction === 'asc'
                    ? 'desc'
                    : 'asc'
            ),
        ]
    );
};

$sortIcon = function ($field) use ($sort, $direction) {
    if ($sort !== $field) {
        return '<i class="fa fa-sort text-muted"></i>';
    }
    return sprintf('<i class="fa fa-sort-%s"></i>', $direction === 'asc' ? 'up' : 'down');
};
?>

<div class="container">
    <?php if (empty($tasks)): ?>
        <div class="alert alert-info">
            <?= t('No overdue tasks found.') ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-header text-white mb-2">
                <?= t('Task List') ?>
            </div>
            <div class="kb-table-container">
                <table class="kb-table">
                    <thead class="table-light">
                    <tr>
                        <th>
                            <a href="<?= $sortUrl('title') ?>" class="kpi-sort-link">
                                <?= t('Title') ?> <?= $sortIcon('title') ?>
                            </a>
                        </th>
                        <th width="150">
                            <a href="<?= $sortUrl('assignee') ?>" class="kpi-sort-link">
                                <?= t('Assignee') ?> <?= $sortIcon('assignee') ?>
                            </a>
                        </th>
                        <th width="200">
                            <a href="<?= $sortUrl('date_started') ?>" class="kpi-sort-link">
                                <?= t('Start Date') ?> <?= $sortIcon('date_started') ?>
                            </a>
                        </th>
                        <th width="200">
                            <a href="<?= $sortUrl('date_due') ?>" class="kpi-sort-link">
                                <?= t('Due Date') ?> <?= $sortIcon('date_due') ?>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $taskId = isset($task['id']) ? $task['id'] : 0;
                        $taskProjectId = isset($task['project_id']) ? $task['project_id'] : $projectId;
                        $taskOwnerId = isset($task['owner_id']) ? $task['owner_id'] : null;
                        ?>
                        <tr>
                            <td>
                                <?php if (!empty($task['description'])): ?>
                                    <?= $this->modal->small(
                                        'file',
                                        '',
                                        'BoardTooltipController',
                                        'description',
                                        ['task_id' => $taskId, 'project_id' => $taskProjectId]
                                    ) ?>
                                <?php else: ?>
                                    <i class="fa fa-file ms-1 me-1" style="color: #6c757da0"></i>
                                <?php endif; ?>

                                <div class="dropdown">
                                    <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                        <strong>#<?= $this->text->e($taskId) ?> <i class="fa fa-caret-down"></i></strong>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?= $this->modal->large(
                                                'edit',
                                                t('Edit the task'),
                                                'TaskModificationController',
                                                'edit',
                                                ['task_id' => $taskId, 'project_id' => $projectId],
                                                false, '', true
                                            ) ?>
                                        </li>
                                        <li>
                                            <?= $this->modal->medium(
                                                'comment',
                                                t('Add a comment'),
                                                'CommentController',
                                                'create',
                                                ['task_id' => $taskId, 'project_id' => $projectId],
                                                false, '', true
                                            ) ?>
                                        </li>
                                        <li>
                                            <?= $this->modal->medium(
                                                'times',
                                                t('Close this task'),
                                                'TaskStatusController',
                                                'close',
                                                ['task_id' => $taskId],
                                                false, '', true
                                            ) ?>
                                        </li>
                                        <li>
                                            <?= $this->modal->medium(
                                                'trash',
                                                t('Remove'),
                                                'taskSuppressionController',
                                                'confirm',
                                                ['task_id' => $taskId],
                                                false, '', true
                                            ) ?>
                                        </li>
                                    </ul>
                                </div>
                                <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', [
                                    'task_id' => $taskId,
                                    'project_id' => $projectId
                                ], 'task-board-title') ?>
                            </td>
                            <td>
                                <?= $taskOwnerId ?: '-' ?>
                            </td>
                            <td>
                                <?= ($task['date_started'] ?? 0) > 0 ? date('l, F j, Y', $task['date_started']) : '-' ?>
                            </td>
                            <td>
                                <?= ($task['date_due'] ?? 0) > 0 ? date('l, F j, Y', $task['date_due']) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <div class="kpi-pagination mt-4">
        <?= $paginator ?>
    </div>
</div>