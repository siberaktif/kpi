<?php
namespace Kanboard\Plugin\KPI\Model;

use Kanboard\Core\Base;

class KPIModel extends Base
{
    const TABLE = 'kpi_definition';

    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('title')
            ->findAll();
    }

    public function getKpiAssignTasks($task_id)
    {
        return $this->db
            ->table('kpi_definition')
            ->columns(
                'kpi_definition.*',
                'ka.*',
                'DATE(FROM_UNIXTIME(kpi_definition.timeline_started)) AS timeline_start',
                'DATE(FROM_UNIXTIME(kpi_definition.timeline_completed)) AS timeline_complete'
            )
            ->left('kpi_assignment', 'ka', 'kpi_id', 'kpi_definition', 'id')
            ->eq('ka.task_id', $task_id)
            ->asc('title')
            ->findOne();
    }

    public function updateKpiActualPoints($kpi_id, $task_id)
    {

    }

    public function getByTaskId($taskId)
    {
        return $this->db
            ->table('kpi_assignment')
            ->columns(
                'kpi_assignment.*',
                'kd.target_unit'
            )
            ->left(
                'kpi_definition',
                'kd',
                'id',
                'kpi_assignment',
                'kpi_id'
            )
            ->eq('kpi_assignment.task_id', $taskId)
            ->findOne();
    }

    public function create(int $taskId, int $kpiId, float $points)
    {
        $values = [
            'kpi_id'     => $kpiId,
            'task_id'    => $taskId,
            'task_point' => $points,
        ];

        return $this->db
            ->table('kpi_assignment')
            ->insert($values);

    }

    public function update($taskId, $kpiId, $points)
    {
        $taskId = (int) $taskId;
        $kpiId  = (int) $kpiId;
        $points = (float) $points;

        $assignment = $this->db
            ->table('kpi_assignment')
            ->eq('task_id', $taskId)
            ->findOne();

        if (! empty($assignment)) {
            $this->db
                ->table('kpi_assignment')
                ->eq('task_id', $taskId)
                ->update([
                    'kpi_id'     => $kpiId,
                    'task_point' => $points,
                ]);
        } else {
            $this->create($taskId, $kpiId, $points);
        }
    }

    public function deleteByTaskId($taskId)
    {
        return $this->db
            ->table('kpi_task')
            ->eq('task_id', (int) $taskId)
            ->remove();
    }
}
