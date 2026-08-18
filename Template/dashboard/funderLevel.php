<?php $userId = $this->user->getId(); ?>

<div class="container">
    <div class="container pb-2">
        <div class="kb-row">
            <div class="kb-col kb-col-12">
                <div class="d-flex">
                    <div class="btn btn-round">
                        <?= $this->url->icon('arrow-left','','DashboardController','show',['user_id' => $userId]) ?>
                    </div>
                    <div class="ms-4" style="font-size: 2rem;">Multi Project Overview</div>
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
        <div class="dashboard-card completed">
            <div class="card-icon">
                <i class="fa fa-sitemap"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Manage Projects') ?></div>
                <div class="card-value"><?= $stats['completed'] ?: 0 ?></div>
                <div class="card-footer">
                    <?= $this->url->icon('arrow-right', 'View Projects', 'FunderController', 'index', [
                        'project_id' => $project['id'],'plugin' => 'KPI', ], false)?>
                </div>
            </div>
        </div>

        <div class="dashboard-card info">
            <div class="card-icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="card-content">
                <div class="card-title"><?= t('Departments') ?></div>
                <div class="card-value"><?= $stats['open'] ?: 0 ?></div>
                <div class="card-footer">
                <?= $this->url->icon('arrow-right', 'View Departments', 'ProjectController', 'index', [
                        'project_id' => $project['id'], 'plugin' => 'KPI', ], false)?>
                </div>
            </div>
        </div>

        <div class="dashboard-card map">
            <div class="card-icon">
                <i class="fa fa-map"></i>
            </div>
                <div class="card-content">
                <div class="card-content">
                    <div class="card-title"><?= t('Manage Areas') ?></div>
                    <div class="card-value"><?= $stats['open'] ?: 0 ?></div>
                    <div class="card-footer">
                    <?= $this->url->icon('arrow-right', 'View Manage areas', 'TaskController', 'task_open', [
                            'project_id' => $project['id'], 'plugin' => 'KPI', ], false)?>
                    </div>
                </div>
            </div>
        </div>

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