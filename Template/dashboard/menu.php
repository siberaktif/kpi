<?php
$action = 'project';

if ($this->user->isAdmin()) {
    $action = 'multiProjectOverview';
}
?>
<li>
     <?= $this->url->icon('bar-chart',t('Multi-Project KPIs'), 'DashboardController', $action, 
     [
        'project_id' => $project['id'],
        'plugin' => 'KPI'
     ]) ?>
</li>