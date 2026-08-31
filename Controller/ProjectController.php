<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class ProjectController extends BaseController
{
    public function index()
    {
        $projects = $this->projectModel->getAll();

        $this->response->html(
            $this->helper->layout->app(
                'KPI:project/project_list',
                [
                    'title'    => t('Project Departments'),
                    'projects' => $projects,
                ]
            )
        );
    }
}
