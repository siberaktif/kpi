<?php
namespace Kanboard\Plugin\KPI;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    private $kpiTaskValues = [
        'kpi_id'     => 0,
        'kpi_points' => 0,
    ];

    public function initialize()
    {
        $this->route->addRoute('/kpi', 'KPIController', 'index', 'KPI');
        $this->route->addRoute('/kpi/create', 'KPIController', 'create', 'KPI');
        $this->route->addRoute('/kpi/save', 'KPIController', 'save', 'KPI');
        $this->route->addRoute('/kpi/edit/:id', 'KPIController', 'edit', 'KPI');
        $this->route->addRoute('/kpi/update/:id', 'KPIController', 'update', 'KPI');
        $this->route->addRoute('/kpi/remove/:id', 'KPIController', 'remove', 'KPI');

        // Task Routes
        $this->route->addRoute('/kpi/task_open', 'TaskController', 'task_open', 'KPI');
        $this->route->addRoute('/kpi/task_overdue', 'TaskController', 'task_overdue', 'KPI');
        $this->route->addRoute('/kpi/task_completed', 'TaskController', '       task_completed', 'KPI');

        $this->container['dashboardService'] = $this->container->factory(function ($c) {
            return new \Kanboard\Plugin\KPI\Service\DashboardService($c);
        });

        $this->container['dashboardService'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Service\DashboardService($c);
        };

        $this->container['kpiModel'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Model\KpiModel($c);
        };

        $this->container['projectDataService'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Service\ProjectDataService($c);
        };

        $this->container['assigneeAvatarService'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Helper\AssigneeAvatarHelper($c);
        };

        $this->template->hook->attachCallable(
            'template:task:form:second-column',
            'KPI:task/form',
            function ($params) {

                $selectedKpi = null;
                $kpiPoints   = 0;

                $taskId = $this->request->getIntegerParam('task_id');

                if ($taskId === 0) {
                    $projectId = $this->request->getIntegerParam('project_id');
                } else {
                    $projectId = $this->projectDataService->getProjectIdByTaskId($taskId);
                    $kpiTask   = $this->kpiModel->getByTaskId($taskId);

                    if ($kpiTask) {
                        $selectedKpi = $kpiTask['id'];
                        $kpiPoints   = $kpiTask['task_point'];
                    }
                }

                $kpis = $this->kpiModel->getAll($projectId);

                return [
                    'kpis'         => $kpis,
                    'selected_kpi' => $selectedKpi,
                    'kpi_points'   => $kpiPoints,
                ];
            }
        );

        $this->hook->on(
            'model:task:modification:prepare',
            function (&$values) {

                $taskId = $this->request->getIntegerParam('task_id');
                // $projectId = $this->request->getIntegerParam('project_id');
                // $kpi_id = $this->request->getIntegerParam('kpi_id');

                $kpiId = isset($values['kpi_id'])
                    ? (int) $values['kpi_id']
                    : 0;

                $points = isset($values['kpi_points'])
                    ? (float) $values['kpi_points']
                    : 0;

                //$kpi_unit

                if ($kpiId > 0 || $taskId > 0) {
                    $this->kpiModel->update(
                        $taskId,
                        $kpiId,
                        $points
                    );
                }

                // VERY IMPORTANT
                unset($values['kpi_id']);
                unset($values['kpi_points']);
            }
        );

        $this->hook->on(
            'model:task:creation:prepare',
            function (&$values) {

                $this->kpiTaskValues = [
                    'kpi_id' => isset($values['kpi_id'])
                        ? (int) $values['kpi_id']
                        : 0,

                    'points' => isset($values['kpi_points'])
                        ? (float) $values['kpi_points']
                        : 0,
                ];

                // IMPORTANT: remove custom fields
                unset($values['kpi_id']);
                unset($values['kpi_points']);
            }
        );

        $this->hook->on(
            'model:task:creation:aftersave',
            function ($taskId, $values) {

                $kpiId  = $this->kpiTaskValues['kpi_id'];
                $points = $this->kpiTaskValues['points'];

                if ($kpiId > 0 || $taskId > 0) {
                    $this->kpiModel->update(
                        $taskId,
                        $kpiId,
                        $points
                    );
                }

            }
        );

        //when board closes
        $this->on('task.close', function ($task) {

        });

        // Register Assets
        $this->hook->on(
        'template:layout:css', [
            'template' => 'plugins/KPI/Asset/css/kanboard-overrides.css',
        ]);

        $this->hook->on('template:layout:js', [
            'template' => 'plugins/KPI/Asset/js/chart.min.js',
        ]);

        $this->hook->on('template:layout:js', [
            'template' => 'plugins/KPI/Asset/js/dashboard.js',
        ]);

        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/dashboard.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/plugin.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/table.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/form.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/app.css']);

        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/kpi.js']);
        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/table.js']);

        $this->template->hook->attach('template:project:dropdown', 'KPI:project/dropdown');
        // $this->template->hook->attach('template:dashboard:sidebar', 'KPI:dashboard/sidebar');

        // Top Menu
        //$this->template->hook->attach('template:header:dropdown', 'KPI:dashboard/menu');
        $this->template->hook->attach('template:project-header:view-switcher', 'KPI:project_header/views');
    }

    public function getPluginName()
    {
        return 'KPI';
    }

    public function getPluginDescription()
    {
        return 'Employee and Project KPI Dashboard for Kanboard';
    }

    public function getPluginAuthor()
    {
        return 'Rey Mark S. Baload';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.40';
    }
}
