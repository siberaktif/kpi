<?php
namespace Kanboard\Plugin\KPI;

use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    private $kpiTaskValues = [
        'kpi_id'     => 0,
        'kpi_points' => 0,
    ];

    public function initialize()
    {
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

        $this->container['kpiFormHelper'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Helper\KpiFormHelper($c);
        };

        $this->container['taskStatusModel'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Model\KpiTaskStatusModel($c);
        };

        $this->container['multiProjectModel'] = function ($c) {
            return new \Kanboard\Plugin\KPI\Model\MultiProjectModel($c);
        };

        $this->container['assigneeAvatarService'] = function ($c) {
            $helper = new \Kanboard\Plugin\KPI\Helper\AssigneeAvatarHelper($c);
            try {
                $helper->setMultiselectMemberModel(
                    $c['multiselectMemberModel']
                );
            } catch (\Exception $e) {
                // Group Assign is not installed.
            }
            return $helper;
        };

        //attach to header
        if ($this->request->getStringParam('plugin') === 'KPI') {
            $this->template->setTemplateOverride('header', 'KPI:header');
            $this->template->setTemplateOverride('project_header/search', 'KPI:project_header/search');
        }

        $this->template->hook->attachCallable(
            'template:board:task:footer',
            'KPI:board/task_footer',
            function ($task) {
                $kpi = $this->kpiModel->getKpiAssignTasks((int) $task['id']);
                return [
                    'kpi' => $kpi,
                ];
            }
        );

        $this->template->hook->attachCallable(
            'template:task:form:second-column',
            'KPI:task/form',
            function ($params) {

                $selectedKpi   = null;
                $kpiPoints     = 0;
                $kpiTargetUnit = 0;

                $taskId = $this->request->getIntegerParam('task_id');

                if ($taskId > 0) {
                    $projectId = $this->projectDataService->getProjectIdByTaskId($taskId);
                    $kpiTask   = $this->kpiModel->getByTaskId($taskId);

                    if ($kpiTask) {
                        $selectedKpi   = $kpiTask['kpi_id'];
                        $kpiPoints     = $kpiTask['task_point'];
                        $kpiTargetUnit = $kpiTask['target_unit'];
                    }
                } else {
                    $projectId = $this->request->getIntegerParam('project_id');
                }

                if ($projectId <= 0) {
                    return [
                        'kpis'         => [],
                        'selected_kpi' => null,
                        'kpi_points'   => 0,
                        'target_unit'  => '',
                    ];
                }

                $kpis = $this->kpiModel->getAll($projectId);

                return [
                    'kpis'         => $kpis,
                    'selected_kpi' => $selectedKpi,
                    'kpi_points'   => $kpiPoints,
                    'target_unit'  => $kpiTargetUnit,
                ];
            }
        );

        $this->hook->on(
            'model:task:modification:prepare',
            function (&$values) {

                $taskId = $this->request->getIntegerParam('task_id');

                $kpiId = isset($values['kpi_id'])
                    ? (int) $values['kpi_id']
                    : 0;

                $points = isset($values['kpi_points'])
                    ? (float) $values['kpi_points']
                    : 0;

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
            function ($taskId) {

                $kpiId  = $this->kpiTaskValues['kpi_id'];
                $points = $this->kpiTaskValues['points'];

                if ($kpiId > 0 || $taskId > 0) {
                    $this->kpiModel->create(
                        $taskId,
                        $kpiId,
                        $points
                    );
                }

            }
        );

        // Register Assets
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/kanboard-overrides.css']);
        // $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/fontawesome/css/all.min.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/buttons.css']);
        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/chart.min.js']);
        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/dashboard.js']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/dashboard.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/plugin.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/table.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/form.css']);
        $this->hook->on('template:layout:css', ['template' => 'plugins/KPI/Asset/css/app.css']);
        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/kpi.js']);
        $this->hook->on('template:layout:js', ['template' => 'plugins/KPI/Asset/js/table.js']);

        $this->template->hook->attach('template:project:dropdown', 'KPI:project/dropdown');
        $this->template->hook->attach('template:header:dropdown', 'KPI:header/user_dropdown');
        $this->template->hook->attach('template:dashboard:page-header:menu', 'KPI:dashboard/menu');
        $this->template->hook->attach('template:project-header:view-switcher', 'KPI:project_header/views');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
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
        return '>=1.2.53';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/rmsbal/kpi';
    }
}
