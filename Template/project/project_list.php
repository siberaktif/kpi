<div class="container">
    <div class="container pb-2">
        <div class="kb-row">
            <div class="kb-col kb-col-6">
                <div class="btn btn-round">
                    <?= $this->url->icon('arrow-left','','DashboardController','multiProjectOverview',['plugin' => 'KPI']) ?>
                </div>
                 <div class="btn btn-success">
                    <?= $this->modal->small('plus',t('Add Project'),'ProjectCreationController','create') ?>
                </div>
            </div>
            <div class="kb-col kb-col-6">
                <div class="">
                    <span style="font-size: 1rem;"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pb-2">
            <strong>Project Department List</strong>
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
                <?php foreach($projects AS $project): ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                <strong>#<?= $this->text->e($project['id']) ?>
                                    <i class="fa fa-caret-down"></i>
                                </strong>
                            </a>
                            <ul>
                                <li>
                                    <?= $this->modal->large('edit', t('Edit Project'),'ProjectEditController','show',['project_id' => $project['id']]) ?>
                                </li>
                                <li>
                                    <?= $this->url->icon('th', t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
                                </li> 
                                <li>
                                    <?= $this->url->icon('list', t('Listing'), 'TaskListController', 'show', array('project_id' => $project['id'])) ?>
                                </li>
                                <li>
                                    <?= $this->modal->medium('dashboard', t('Activity'), 'ActivityController', 'project', array('project_id' => $project['id'])) ?>
                                </li>
                                <?php if ($this->user->hasProjectAccess('AnalyticController', 'taskDistribution', $project['id'])): ?>
                                    <li>
                                        <?= $this->modal->large('line-chart', t('Analytics'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
                                    </li>
                                <?php endif ?>
                                <?php if ($this->user->hasProjectAccess('ProjectEditController', 'show', $project['id'])): ?>
                                    <li>
                                        <?= $this->url->icon('cog', t('Configure this project'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
                                    </li>
                                <?php endif ?>
                                <li>
                                    <?= $this->modal->small('trash',t('Remove'),'ProjectStatusController','confirmRemove',['project_id' => $project['id']]) ?>
                                </li>
                            </ul>
                        </div>
                        <?= $project['name'] ?>
                    </td>
                    <td><?= '' ?></td>
                    <td class="click-truncate"><?= $this->text->markdown($project['description']) ?></td>
                    <td><?= '' ?></td>
                    <td><?= '' ?></td>
                    <td class="kb-text-center"><?= '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>