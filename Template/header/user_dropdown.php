<?php if ($this->user->isAdmin()): ?>
<li>
    <?= $this->url->icon('bar-chart', t('Multi-Project KPIs'), 'DashboardController', 'multiProjectOverview', ['plugin' => 'KPI']) ?>
</li>
<?php endif; ?>