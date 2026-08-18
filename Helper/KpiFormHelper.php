<?php
namespace Kanboard\Plugin\KPI\Helper;

use Kanboard\Helper\FormHelper;

class KpiFormHelper extends FormHelper
{
    public function selectOptionBuilder(
        string $table,
        string $keyColumn,
        string $labelColumn,
        string $placeholder = 'Select...'
    ): array {
        $rows = $this->db
            ->table($table)
            ->columns($keyColumn, $labelColumn)
            ->findAll();

        $options = ['' => $placeholder];

        foreach ($rows as $row) {
            $options[$row[$keyColumn]] = $row[$labelColumn];
        }

        return $options;
    }
}
