<?php
$action = 'project';

if ($this->user->isAdmin()) {
    $action = 'multiProjectOverview';
}

// Proje değişkenini güvenli hale getir
$projectId = isset($project['id']) ? $project['id'] : 0;
?>
<li>
     <?= $this->url->icon('bar-chart', t('Multi-Project KPIs'), 'DashboardController', $action, 
     [
        'project_id' => $projectId,
        'plugin' => 'KPI'
     ]) ?>
</li>