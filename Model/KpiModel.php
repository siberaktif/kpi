<?php
namespace Kanboard\Plugin\KPI\Model;

use Kanboard\Core\Base;

// class KPIModel extends Base
class KpiModel extends Base
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

    public function updateKpiActualPoints(int $taskId, $operation = true)
    {
        $assignment = $this->db
            ->table('kpi_assignment')
            ->columns('kpi_id', 'task_point')
            ->eq('task_id', (int) $taskId)
            ->findOne();

        if (! $assignment) {
            return false;
        }

        $kpi = $this->db
            ->table('kpi_definition')
            ->columns('actual', 'target')
            ->eq('id', (int) $assignment['kpi_id'])
            ->findOne();

        if (! $kpi) {
            return false;
        }

        $actual   = (float) $kpi['actual'];
        $points   = (float) $assignment['task_point'];
        $target   = (float) $kpi['target'];
        $kdStatus = 1;

        if (! $operation) {
            $actual   -= $points;
            $isActive  = 1;
        } else {
            $actual   += $points;
            $isActive  = 0;
        }

        // Never allow negative actual points
        $actual = max(0, $actual);

        if ($actual >= $target) {
            $status   = 'Done';
            $kdStatus = 0;
        } elseif ($actual > 0) {
            $status = 'ONGOING';
        } else {
            $status = 'PENDING';
        }

        $updateKd = $this->db
            ->table('kpi_definition')
            ->eq('id', (int) $assignment['kpi_id'])
            ->update([
                'actual' => $actual,
                'status' => $status,
                'active' => $kdStatus,
            ]);

        if (! $updateKd) {
            error_log('KPI:kpiModel failed to update kpi_definition');
        }

        $updateKa = $this->db
            ->table('kpi_assignment')
            ->eq('id', (int) $taskId)
            ->update([
                'is_active' => $isActive,
            ]);

        if (! $updateKa) {
            error_log('KPI:kpiModel failed to update kpi_assignment');
        }
    }

    public function getByTaskId($taskId)
    {
        return $this->db
            ->table('kpi_assignment')
            ->columns(
                'kpi_assignment.*',
                'kd.target_unit',
                'kd.target'
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
