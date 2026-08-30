<div class="container">
    <div class="container pb-2">
        <div class="kb-row">
            <div class="kb-col kb-col-6">
                <div class="btn btn-round">
                    <?= $this->url->icon('arrow-left','','DashboardController','multiProjectOverview',['plugin' => 'KPI']) ?>
                </div>
                 <div class="btn btn-success">
                    <?= $this->modal->large('plus',t('Add Project'),'FunderController','create',['plugin' => 'KPI']) ?>
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
            <strong><?= t('Funders and Projects') ?></strong>
        </div>
        <table class="kb-table kb-table-striped">
            <thead>
                <tr>
                    <th style="width: 300px;"><?= t('Project Name') ?></th>
                    <th style="width: 300px;"><?= t('Funder Name') ?></th>
                    <th style="width: 400px;"><?= t('Description') ?></th>
                    <th style="width: 210px;"><?= t('Date started') ?></th>
                    <th style="width: 210px;"><?= t('Date Completed') ?></th>
                    <th style="width: 100px;"><?= t('Year Duration') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($funders AS $funder): ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <a href="#" class="dropdown-menu dropdown-menu-link-icon">
                                <strong>#<?= $this->text->e($funder['id']) ?>
                                    <i class="fa fa-caret-down"></i>
                                </strong>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->modal->large(
                                        'edit',
                                        t('Edit Funder Informations'),
                                        'FunderController',
                                        'edit',
                                        [
                                            'funder_id' => $funder['id'],
                                            'plugin' => 'KPI'
                                        ],
                                        false,
                                        '',
                                        true
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->modal->small(
                                        'trash',
                                        t('Remove'),
                                        'KPIController',
                                        'confirm',
                                        [
                                            'plugin' => 'KPI'
                                        ],
                                        false,
                                        '',
                                        true
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                        <?= $funder['project_alias'] ?>
                    </td>
                    <td><?= $funder['funder_name'] ?></td>
                    <td class="click-truncate"><?= $funder['description'] ?></td>
                    <td><?= date('F Y', $funder['date_started']) ?></td>
                    <td><?= date('F Y', $funder['date_completed']) ?></td>
                    <td class="kb-text-center"><?= $funder['year_duration'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
