<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class DashboardController extends BaseController
{

    public function project()
    {
        $project  = $this->getProject();
        $projects = $this->projectModel->getAll();

        $stats     = $this->dashboardService->getProjectStats($project['id']);
        $kpiStats  = $this->dashboardService->getKpiStats($project['id']);
        $taskTrend = $this->dashboardService->getTaskTrend($project['id']);

        $this->response->html(
            $this->helper->layout->app(
                'KPI:dashboard/projectLevel',
                [
                    'project'     => $project,
                    'projects'    => $projects,
                    'stats'       => $stats,
                    'kpiStats'    => $kpiStats,
                    'taskTrend'   => $taskTrend,
                    'title'       => $project['name'],
                    'description' => $this->helper->projectHeader->getDescription($project),
                ]
            )
        );
    }

    public function department()
    {
        $project  = $this->getProject();
        $projects = $this->projectModel->getAll();

        $stats     = $this->dashboardService->getProjectStats($project['id']);
        $kpiStats  = $this->dashboardService->getKpiStats($project['id']);
        $taskTrend = $this->dashboardService->getTaskTrend($project['id']);

        $this->response->html(
            $this->helper->layout->app(
                'KPI:dashboard/departmentLevel',
                [
                    'project'     => $project,
                    'projects'    => $projects,
                    'stats'       => $stats,
                    'kpiStats'    => $kpiStats,
                    'taskTrend'   => $taskTrend,
                    'title'       => $project['name'],
                    'description' => $this->helper->projectHeader->getDescription($project),
                ]
            )
        );
    }

    public function multiProjectOverview()
    {
        $this->response->html(
            $this->helper->layout->app(
                'KPI:dashboard/funderLevel',
                [
                    'title'       => t('Key Performance Indicator')
                ]
            )
        );
    }

}
