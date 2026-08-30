<?php
// Güvenli varsayılanlar — hata loglarını temizler
$kpiStats  = $kpiStats ?? [];
$stats     = $stats ?? [];
$project   = $project ?? [];
$taskTrend = $taskTrend ?? ['labels' => [], 'percentage' => []];
?>
<?php $userId = $this->user->getId(); ?>

<div class="container">
    <div class="container pb-2">
        <div class="kb-row">
            <div class="kb-col kb-col-12">
                <div class="d-flex">
                    <div class="btn btn-round">
                        <?= $this->url->icon('arrow-left','','DashboardController','show',['user_id' => $userId]) ?>
                    </div>
                    <div class="ms-4" style="font-size: 2rem;"><?= t('Multi Project Overview') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card primary">
            <div class="card-icon">
                <i class="fa fa-line-chart"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Overall KPI') ?></div>
                <div class="card-value"><?= $kpiStats['kpiProg'] ?: 0 ?>%</div>
                <div class="card-footer"><?= t('Overall Project Performance') ?></div>
            </div>
        </div>
        <a href="<?= $this->url->href('FunderController', 'index', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer" style="">
            <div class="dashboard-card completed">
                <div class="card-icon">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="card-content">
                    <div class="card-title"><?= t('Manage Projects') ?></div>
                    <div class="card-value"><?= $stats['completed'] ?: 0 ?></div>
                    <div class="card-footer">
                        <i class="fa fa-arrow-right"></i> <?= t('View Projects') ?>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?= $this->url->href('ProjectController', 'index', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer"  style="">
            <div class="dashboard-card info">
                <div class="card-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="card-content">
                    <div class="card-title"><?= t('Departments') ?></div>
                    <div class="card-value"><?= $stats['open'] ?: 0 ?></div>
                    <div class="card-footer">
                        <i class="fa fa-arrow-right"></i> <?= t('View Departments') ?>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?= $this->url->href('TaskController', 'task_open', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" target="_blank" rel="noopener noreferrer">
            <div class="dashboard-card map">
                <div class="card-icon">
                    <i class="fa fa-map"></i>
                </div>
                <div class="card-content">
                    <div class="card-title"><?= t('Manage Areas') ?></div>
                    <div class="card-value"><?= $stats['open'] ?: 0 ?></div>
                    <div class="card-footer">
                        <i class="fa fa-arrow-right"></i> <?= t('View Manage areas') ?>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="dashboard-chart-grid">
        <div class="dashboard-panel">
            <h3><?= t('Projects Status') ?></h3>
            <div class="chart-container">
                <canvas id="kpiChart"></canvas>
            </div>
        </div>

        <div class="dashboard-panel">
            <h3><?= t('Deparments Status') ?></h3>
            <div class="chart-container">
                <canvas id="taskChart"></canvas>
            </div>
        </div>
    </div>

    <div id="kpi-dashboard-data" 
        data-completed="<?= $stats['completed'] ?>" 
        data-open="<?= $stats['open'] ?>"
        data-overdue="<?= $stats['overdue'] ?>" 
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