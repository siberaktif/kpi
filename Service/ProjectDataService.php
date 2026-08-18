<?php
namespace Kanboard\Plugin\KPI\Service;

use Kanboard\Core\Base;

class ProjectDataService extends Base
{
    public function getProjectIdByTaskId($taskId)
    {
        $task = $this->db
            ->table('tasks')
            ->columns('project_id')
            ->eq('id', $taskId)
            ->findOne();

        return $task['project_id'];
    }

    public function getKpiProjects()
    {
        $projectIds = $this->db
            ->table('kpi_definition')
            ->columns('project_id')
            ->distinct()
            ->findAllByColumn('project_id');

        $projects = $this->db
            ->table('projects')
            ->columns('id AS projectId', 'name')
            ->in('id', $projectIds)
            ->findAll();

        $kpiInfo = $this->db
            ->table('kpi_funder')
            ->columns('project_alias')
            ->distinct()
            ->findAllByColumn('project_alias');
        
        //$project['funder_name'] = $kpiInfo[0];

        return $projects;
    }

    public function getProjectFunders()
    {
        $funders = $this->db
            ->table('kpi_funder')
            ->columns('id', 'project_alias AS shortname')
            ->findAll();

        $result = [];

        foreach ($funders as $funder) {
            $result[$funder['id']] = $funder['shortname'];
        }

        return $result;
    }
}
