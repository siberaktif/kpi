<?php
namespace Kanboard\Plugin\KPI\Model;

use Kanboard\Model\TaskStatusModel;

class KpiTaskStatusModel extends TaskStatusModel
{
    public function close($taskId)
    {
        $result = parent::close($taskId);

        if ($result) {
            $this->kpiModel->updateKpiActualPoints($taskId);
        }

        return $result;
    }

    public function open($taskId)
    {
        $result = parent::open($taskId);

        if ($result) {           
            $this->kpiModel->updateKpiActualPoints($taskId, false);
        }

        return $result;
    }
}
