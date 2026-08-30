<?php
$projects = $projects ?? [];
?>
<div class="container">
    <div class="container pb-2">
        <div class="kb-row">
            <div class="kb-col kb-col-6">
                <div class="btn btn-round">
                    <?= $this->url->icon('arrow-left','','DashboardController','multiProjectOverview',['plugin' => 'KPI']) ?>
                </div>
                <div class="btn btn-success">
                    <?= $this->modal->small('plus', t('Add Project'), 'ProjectCreationController', 'create') ?>
                </div>
            </div>
            <div class="kb-col kb-col-6">
                <div class=""><span style="font-size: 1rem;"></span></div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pb-2">
            <strong><?= t('Project Department List') ?></strong>
        </div>
        <table class="kb-table kb-table-striped">
            <thead>
                <tr>
                    <th style="width: 300px;"><?= t('Department Name') ?></th>
                    <th style="width: 300px;"><?= t('Project Funder') ?></th>
                    <th style="width: 400px;"><?= t('Description') ?></th>
                    <th style="width: 210px;"><?= t('Date started') ?></th>
                    <th style="width: 210px;"><?= t('Date Completed') ?></th>
                    <th style="width: 100px;"><?= t('Year Duration') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <?php
                    $projectId = isset($project['id']) ? $project['id'] : 0;
                    $projectName = isset($project['name']) ? $project['name'] : '';
                    $projectDescription = isset($project['description']) ? $project['description'] : '';
                    ?>
                    <tr>
                        <td>
                            <div class="dropdown">
                                <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                    <strong>#<?= $this->text->e($projectId) ?> <i class="fa fa-caret-down"></i></strong>
                                </a>
                                <ul>
                                    <li><?= $this->modal->large('edit', t('Edit Project'), 'ProjectEditController', 'show', ['project_id' => $projectId]) ?></li>
                                    <li><?= $this->url->icon('th', t('Board'), 'BoardViewController', 'show', ['project_id' => $projectId]) ?></li>
                                    <li><?= $this->url->icon('list', t('Listing'), 'TaskListController', 'show', ['project_id' => $projectId]) ?></li>
                                    <li><?= $this->modal->medium('dashboard', t('Activity'), 'ActivityController', 'project', ['project_id' => $projectId]) ?></li>
                                    <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $projectId)): ?>
                                        <li><?= $this->modal->large('line-chart', t('Analytics'), 'AnalyticController', 'taskDistribution', ['project_id' => $projectId]) ?></li>
                                    <?php endif ?>
                                    <?php if ($this->user->hasProjectAccess('ProjectEditController', 'show', $projectId)): ?>
                                        <li><?= $this->url->icon('cog', t('Configure this project'), 'ProjectViewController', 'show', ['project_id' => $projectId]) ?></li>
                                    <?php endif ?>
                                    <li><?= $this->modal->small('trash', t('Remove'), 'ProjectStatusController', 'confirmRemove', ['project_id' => $projectId]) ?></li>
                                </ul>
                            </div>
                            <?= $projectName ?>
                        </td>
                        <td><?= '' ?></td>
                        <td class="click-truncate"><?= $this->text->markdown($projectDescription) ?></td>
                        <td><?= '' ?></td>
                        <td><?= '' ?></td>
                        <td class="kb-text-center"><?= '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>