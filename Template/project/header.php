<?php if($project): ?>
<a href="<?= $this->url->href('KPIController', 'index', ['project_id' => $project['id'], 'plugin' => 'KPI']) ?>" class="kpi-header-button"><i class="fa-solid fa-chart-bar"></i> Manage KPI</a>
<?php endif; ?>