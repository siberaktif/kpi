<?php
$kpiStats  = $kpiStats ?? [];
$stats     = $stats ?? [];
$project   = $project ?? [];
$taskTrend = $taskTrend ?? ['labels' => [], 'percentage' => []];
?>
<?= $this->projectHeader->render($project,'DashboardController','project',false,'KPI');?>

<div class="container">
<div class="dashboard-header">
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

    <a href="<?= $this->url->href('TaskController', 'task_completed', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer" style="display:block; text-decoration:none; color:inherit;">
        <div class="dashboard-card completed">
            <div class="card-icon">
                <i class="fa fa-check"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Completed Tasks') ?></div>
                <div class="card-value"><?= $stats['completed'] ?></div>
                <div class="card-footer"><?= t('Finished Tasks') ?></div>
            </div>
        </div>
    </a>

    <a href="<?= $this->url->href('TaskController', 'task_open', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer" style="display:block; text-decoration:none; color:inherit;">
        <div class="dashboard-card info">
            <div class="card-icon">
                <i class="fa fa-folder-open"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Open Tasks') ?></div>
                <div class="card-value"><?= $stats['open'] ?></div>
                <div class="card-footer"><?= t('Currently Active') ?></div>
            </div>
        </div>
    </a>

    <a href="<?= $this->url->href('TaskController', 'task_overdue', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer" style="display:block; text-decoration:none; color:inherit;">
        <div class="dashboard-card danger">
            <div class="card-icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Overdue Tasks') ?></div>
                <div class="card-value"><?= $stats['overdue'] ?></div>
                <div class="card-footer"><?= t('Needs Attention') ?></div>
            </div>
        </div>
    </a>

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
        data-progress="<?= $stats['progress'] ?>"
        data-label-completed="<?= t('Completed') ?>"
        data-label-open="<?= t('Open') ?>"
        data-label-overdue="<?= t('Overdue') ?>"
        data-label-done="<?= t('Done') ?>"
        data-label-ongoing="<?= t('Ongoing') ?>"
        data-label-pending="<?= t('Pending') ?>"
        data-label-planned="<?= t('Planned') ?>"
        data-label-scheduled="<?= t('Scheduled') ?>"
        data-label-overall-task="<?= t('Overall Task') ?>"
        data-label-no-data="<?= t('No Data') ?>"
    >
    </div>
</div>