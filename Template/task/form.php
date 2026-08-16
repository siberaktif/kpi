<div class="form-group mt-3">
    <label for="kpi_id">
        <?= t('KPI related to task') ?>
    </label>

    <select
        name="kpi_id"
        id="kpi_id"
        class="form-control"
    >
        <option value="">
            <?= t('Select KPI') ?>
        </option>

        <?php foreach ($kpis as $kpi): ?>
            <?php $kpiTargetUnit = !empty($kpi['target_unit']) ? ' > ' .$kpi['target_unit'] : ''; ?>
            <option
                value="<?= (int) $kpi['id'] ?>"
                <?= (int) $selected_kpi === (int) $kpi['id'] ? 'selected' : '' ?>>
                <?= $this->text->e($kpi['title']. $kpiTargetUnit) ?>
            </option>

        <?php endforeach ?>
    </select>
</div>


<div class="form-group mt-3">
    <?php $unit = !empty($target_unit) ? $target_unit : 'Task KPI Points'; ?>
    <label for="kpi_points">
        <?= t($unit) ?>
    </label>
    <input
        type="number"
        name="kpi_points"
        id="kpi_points"
        class="form-control"
        min="0"
        step="0.01"
        value="<?= $kpi_points !== null
            ? $kpi_points
            : 0 ?>"
    >
</div>