<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class ProjectController extends BaseController
{
    public function index()
    {
        $projects = $this->projectDataService->getKpiProjects();

        $this->response->html(
            $this->helper->layout->app(
                'KPI:project/project_list',
                [
                    'title'    => 'Project Departments',
                    'projects' => $projects,
                ]
            )

        );
    }
}
