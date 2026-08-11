<div class="page-header">
    <h2>
        <?= t('Task') ?> &gt;
        <?= $this->text->e($project['name']) ?>
    </h2>
</div>

<div class="page-header">
    <h2>
        <?= $this->text->e($task['title']) ?>
    </h2>
</div>

 <div class="card shadow-sm">
    <div class="container">
        <table class="kb-table">
            <thead class="table-light">
            <tr>
                <th><?= t('Title') ?></th>
                <th><?= t('Description') ?></th>
                <th><?= t('Start Date') ?></th>
                <th><?= t('Due Date') ?></th>
                <th class="kb-text-center" style="width: 100px;"><?= t('Column') ?></th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#<?= $this->text->e($task['id']) ?>
                        <?= $this->url->link($this->text->e($task['title']),'TaskViewController','show',
                        [
                            'task_id' => $task['id'],
                            'project_id' => $project['id']
                        ],
                        'task-board-title') ?>
                    </td>

                    <td>
                        <?= $task['description'] ?: '-' ?>
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
                        
                    <td>
                        <?= $column['column_name'] ?: '-' ?>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
    <div class="container">
        <ul class="kb-list-group-clean">
            <li class="kb-list-head">
                <?= t('Open with:') ?>
            </li>
             <li class="kb-list-group-item">
                <?= $this->url->icon('calendar', t('Show in Calendar'), 'CalendarController', 'project', array('project_id' => $project['id'], 'plugin' => 'Calendar', 'search' => '#'.$task['id'])) ?>
            </li>
             <li class="kb-list-group-item">
                <?= $this->url->icon('sliders', t('Show in Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'plugin' => 'Gantt', 'search' => '#'.$task['id'])) ?>
            </li>
             <li class="kb-list-group-item">
                <?= $this->url->icon('th', t('Show in Board'), 'BoardViewController', 'show', array('project_id' => $project['id'], 'search' => '#'.$task['id'])) ?>
            </li>
        </ul>
    </div>
</div>