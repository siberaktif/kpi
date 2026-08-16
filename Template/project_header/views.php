<li <?= $this->app->checkMenuSelection('DashboardController') ?>>
    <?= $this->url->icon('bar-chart', t('KPI'), 'DashboardController', 'project', array('project_id' => $project['id'], 'plugin' => 'KPI'), false, 'view-kpi', t('Keyboard shortcut: "%s"', 'v k')) ?> 
</li>