<?= $this->projectHeader->render($project,'KPIController','project',false,'KPI');?>

<div class="container">
<div class="dashboard-header">
    <div class="dashboard-project-selector">
        <label>
            <i class="fa fa-folder-open"></i> 
            <?= t('Project') ?>
        </label>
        <select id="projectSwitcher" class="form-control">
            <?php foreach($projects as $item): ?>
            <option value="<?= $item['id']?>" <?= $item['id']==$project['id']?'selected':'' ?>>
                <?= $this->text->e($item['name']) ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="btn">
        <?= $this->url->icon(
            'cogs',
            t('Manage KPI'),
            'KPIController',
            'index',
            [
                'project_id' => $project['id'],
                'plugin' => 'KPI'
            ],
            false,
        ) ?>

    </div>

</div>

<div class="dashboard-grid">
    <div class="dashboard-card primary">
        <div class="card-icon">
            <i class="fa fa-line-chart"></i>
        </div>
        <div class="card-content">
            <div class="card-title"><?= t('Overall KPI') ?></div>
            <div class="card-value"><?= $kpiStats['kpiProg'] ?>%</div>
            <div class="card-footer"><?= t('Overall Project Performance') ?></div>
        </div>
    </div>
    <div class="dashboard-card completed">
        <div class="card-icon">
            <i class="fa fa-check"></i>
        </div>
        <div class="card-content">
            <div class="card-title"><?= t('Completed Tasks') ?></div>
            <div class="card-value"><?= $stats['completed'] ?></div>
            <div class="card-footer"><?= t('Finished Tasks') ?>
                <?= $this->url->icon('link', '', 'TaskController', 'task_completed', [
                    'project_id' => $project['id'],
                    'plugin' => 'KPI',
                    ],
                    false,
                    'view-completed',
                    t('View completed tasks'),
                    )?>
            </div>
        </div>
    </div>

    <div class="dashboard-card info">
        <div class="card-icon">
            <i class="fa fa-folder-open"></i>
        </div>
        <div class="card-content">
            <div class="card-title"><?= t('Open Tasks') ?></div>
            <div class="card-value"><?= $stats['open'] ?></div>
            <div class="card-footer"><?= t('Currently Active') ?>
            <?= $this->url->icon('link', '', 'TaskController', 'task_open', [
                    'project_id' => $project['id'],
                    'plugin' => 'KPI',
                    ],
                    false,
                    'view-open',
                    t('View open tasks'),
                    )?>
            </div>
        </div>
    </div>

    <div class="dashboard-card danger">
        <div class="card-icon">
            <i class="fa fa-exclamation-triangle"></i>
        </div>

        <div class="card-content">
            <div class="card-title"><?= t('Overdue Tasks') ?></div>
            <div class="card-value"><?= $stats['overdue'] ?></div>
            <div class="card-footer"><?= t('Needs Attention') ?>
            <?= $this->url->icon('link', '', 'TaskController', 'task_overdue', [
                    'project_id' => $project['id'],
                    'plugin' => 'KPI',
                    ],
                    false,
                    'view-overdue',
                    t('View overdue tasks'),
                    )?>
        
            </div>
        </div>
    </div>

    <div class="dashboard-card health">
        <div class="card-icon">
            <i class="fa fa-heart"></i>
        </div>

        <div class="card-content">

            <div class="card-title"><?= t('Project Health') ?></div>

            <div class="card-value">

                <?php if ($kpiStats['kpiProg'] >= 90): ?>

                <span class="health-good"><?= t('Excellent') ?></span>

                <?php elseif ($kpiStats['kpiProg'] >= 75): ?>

                <span class="health-warning"><?= t('Good') ?></span>

                <?php else: ?>

                <span class="health-danger"><?= t('Warning') ?></span>

                <?php endif ?>

            </div>

            <div class="card-footer">
                <?= $kpiStats['kpiProg'] ?>% <?= t('Overall Score') ?>
            </div>

        </div>

    </div>

</div>

<div class="dashboard-chart-grid">

    <div class="dashboard-panel">

        <h3><?= t('KPI Status') ?></h3>

        <div class="chart-container">
            <canvas id="kpiChart"></canvas>
        </div>

    </div>

    <div class="dashboard-panel">

        <h3><?= t('Task Status') ?></h3>

        <div class="chart-container">
            <canvas id="taskChart"></canvas>
        </div>

    </div>
</div>
<div class="dashboard-chart-grid">

<div class="dashboard-panel">

        <h3><?= t('Task Trend') ?></h3>

        <div class="chart-container">
            <canvas id="trendChart"></canvas>
        </div>

    </div>
</div>

    <!-- Data used by dashboard.js -->
    <div id="kpi-dashboard-data" 
        data-completed="<?= $stats['completed'] ?>" 
        data-open="<?= $stats['open'] ?>"
        data-overdue="<?= $stats['overdue'] ?>" 
        data-done="<?= $kpiStats['done'] ?>" 
        data-ongoing="<?= $kpiStats['ongoing'] ?>" 
        data-pending="<?= $kpiStats['pending'] ?>"
        data-planned="<?= $kpiStats['planned'] ?>"
        data-scheduled="<?= $kpiStats['scheduled'] ?>"
        data-taskTrendLabel='<?= json_encode($taskTrend['labels']) ?>'
        data-taskTrendData='<?= json_encode($taskTrend['percentage']) ?>'
        data-progress="<?= $stats['progress'] ?>">
    </div>
</div>