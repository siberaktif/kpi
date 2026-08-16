<?php
$action = 'project';

if ($this->user->isAdmin()) {
    $action = 'multiProjectOverview';
}
?>
<li>
     <?= $this->url->icon('bar-chart',t('My KPI'), 'DashboardController', $action, 
     [
        'project_id' => $project['id'],
        'plugin' => 'KPI'
     ]) ?>
</li>