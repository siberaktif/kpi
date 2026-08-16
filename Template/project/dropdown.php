<?php
$action = 'project';

if ($this->user->isAdmin()) {
    $action = 'multiProjectOverview';
}
?>

<li>
    <?= $this->url->icon('bar-chart', t('KPI'), 'DashboardController', $action, array('project_id' => $project['id'], 'plugin' => 'KPI')) ?>
</li>