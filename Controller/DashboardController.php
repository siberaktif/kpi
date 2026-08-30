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

/**     public function multiProjectOverview()
    {
        $this->response->html(
            $this->helper->layout->app(
                'KPI:dashboard/funderLevel',
                [
                    'title' => t('Key Performance Indicator'),
                ]
            )
        );
    }
    */
        public function multiProjectOverview()
    {
        $projects = $this->projectModel->getAll();
    
        // Eğer proje seçilmediyse, varsayılan olarak ilk projeyi al
        $projectId = $this->request->getIntegerParam('project_id');
        if (empty($projectId) && !empty($projects)) {
            $projectId = $projects[0]['id'];
        }
    
        if ($projectId) {
            $project = $this->projectModel->getById($projectId);
            $stats = $this->dashboardService->getProjectStats($projectId);
            $kpiStats = $this->dashboardService->getKpiStats($projectId);
            $taskTrend = $this->dashboardService->getTaskTrend($projectId);
        } else {
            $project = ['id' => 0, 'name' => ''];
            $stats = [];
            $kpiStats = [];
            $taskTrend = ['labels' => [], 'percentage' => []];
        }
    
        $this->response->html(
            $this->helper->layout->app(
                'KPI:dashboard/funderLevel',
                [
                    'project'     => $project,
                    'projects'    => $projects,
                    'stats'       => $stats,
                    'kpiStats'    => $kpiStats,
                    'taskTrend'   => $taskTrend,
                    'title'       => t('Key Performance Indicator'),
                ]
            )
        );
    }
}
