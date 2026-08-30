<?php
namespace Kanboard\Plugin\KPI\Model;

use Kanboard\Core\Base;

class MultiProjectModel extends Base
{
    public function addFunder(array $values) {
        $this->db->table('kpi_funder')
            ->insert($values);

        return $this->db->getLastId();
    }

    public function updateFunder(int $funder_id, array $values)
    {
        $this->db->table('kpi_funder')
            ->eq('id', $funder_id)
            ->update($values);

        return $this->db->getLastId();
    }

    public function getAllFunderInfo()
    {
        return $this->db
            ->table('kpi_funder')
            ->findAll();
    }

    public function getFunderInfoById($id)
    {
        return $this->db
            ->table('kpi_funder')
            ->eq('id', $id)
            ->findOne();
    }
}
