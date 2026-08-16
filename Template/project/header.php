<?php if($project): ?>
<a href="<?= $this->url->href(
    'KPIController',
    'index',
    ['project_id' => $project['id'], 'plugin' => 'KPI']
) ?>"
class="kpi-header-button">
    <i class="fa fa-cogs"></i>
    Manage KPI
</a>
<?php endif; ?>