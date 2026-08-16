<?php
$action = 'project';

if ($this->user->isAdmin()) {
    $action = 'multiProjectOverview';
}
?>
<li <?= $this->app->checkMenuSelection('DashboardController') ?>>
    <?= $this->url->link(t('My KPI'), 'DashboardController', $action, array('project_id' => $project['id'], 'plugin' => 'KPI')) ?>
</li>