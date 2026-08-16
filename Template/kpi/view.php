<?php if (empty($kpi)): ?>

    <div class="kpi-empty">
        <div class="kpi-empty-icon">
            <i class="fa-solid fa-gauge-high"></i>
        </div>

        <div class="kpi-empty-content">
            <h3><?= t('No KPI assigned') ?></h3>
            <p><?= t('This task is currently not associated with a KPI.') ?></p>
        </div>
    </div>

<?php else: ?>

    <div class="kpi-detail">

        <!-- Header -->
        <div class="kpi-detail-header">

            <div class="kpi-detail-icon">
                <i class="fa-solid fa-gauge-high"></i>
            </div>

            <div class="kpi-detail-heading">
                <div class="kpi-detail-label">
                    <?= t('KEY PERFORMANCE INDICATOR') ?>
                </div>

                <h2>
                    <?= $this->text->e($kpi['title'] ?? 'KPI') ?>
                </h2>

                <span class="kpi-detail-id">
                    KPI #<?= (int) ($kpi['kpi_id'] ?? $kpi['id'] ?? 0) ?>
                </span>
            </div>

        </div>


        <!-- KPI Summary -->
        <div class="kpi-summary">

            <div class="kpi-summary-item">
                <div class="kpi-summary-icon">
                    <i class="fa-solid fa-list-check"></i>
                </div>

                <div>
                    <span><?= t('Task') ?></span>
                    <strong>
                        #<?= (int) ($kpi['task_id'] ?? 0) ?>
                    </strong>
                </div>
            </div>


            <div class="kpi-summary-item">
                <div class="kpi-summary-icon">
                    <i class="fa-solid fa-star"></i>
                </div>

                <div>
                    <span><?= t('Task Points') ?></span>
                    <strong>
                        <?= $this->text->e(number_format($kpi['task_point'] ?? 0)) ?>
                    </strong>
                </div>
            </div>


            <?php if (isset($kpi['target'])): ?>

                <div class="kpi-summary-item">
                    <div class="kpi-summary-icon">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>

                    <div>
                        <span><?= t('Target') ?></span>
                        <strong>
                            <?= $this->text->e(number_format($kpi['target'])) ?>
                        </strong>
                    </div>
                </div>

            <?php endif; ?>

        </div>


        <!-- Timeline -->
        <?php if (
            isset($kpi['timeline_start']) ||
            isset($kpi['timeline_complete'])
        ): ?>

            <div class="kpi-section">

                <div class="kpi-section-title">
                    <i class="fa-solid fa-calendar-days"></i>
                    <?= t('Timeline') ?>
                </div>

                <div class="kpi-timeline">

                    <?php if (isset($kpi['timeline_start'])): ?>

                        <div class="kpi-timeline-item">

                            <div class="kpi-timeline-icon">
                                <i class="fa-solid fa-play"></i>
                            </div>

                            <div class="kpi-timeline-content">
                                <span><?= t('Start Date') ?></span>

                                <strong>
                                    <?= $this->text->e(
                                        $kpi['timeline_start']
                                    ) ?>
                                </strong>
                            </div>

                        </div>

                    <?php endif; ?>


                    <div class="kpi-timeline-line"></div>


                    <?php if (isset($kpi['timeline_complete'])): ?>

                        <div class="kpi-timeline-item">

                            <div class="kpi-timeline-icon">
                                <i class="fa-solid fa-flag-checkered"></i>
                            </div>

                            <div class="kpi-timeline-content">
                                <span><?= t('Completion Date') ?></span>

                                <strong>
                                    <?= $this->text->e(
                                        $kpi['timeline_complete']
                                    ) ?>
                                </strong>
                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- Details -->
        <div class="kpi-section">

            <div class="kpi-section-title">
                <i class="fa-solid fa-circle-info"></i>
                <?= t('KPI Information') ?>
            </div>

            <div class="kpi-info-list">

                <div class="kpi-info-row">
                    <span><?= t('KPI ID') ?></span>
                    <strong>
                        #<?= (int) ($kpi['kpi_id'] ?? $kpi['id'] ?? 0) ?>
                    </strong>
                </div>

                <div class="kpi-info-row">
                    <span><?= t('Task ID') ?></span>
                    <strong>
                        #<?= (int) ($kpi['task_id'] ?? 0) ?>
                    </strong>
                </div>
                <div class="kpi-info-row">
                    <span><?= t('Unit of Measure') ?></span>
                    <strong>
                        <?= $this->text->e(
                            $kpi['target_unit'] ?? 'Not Specify'
                        ) ?>
                    </strong>
                </div>
                <div class="kpi-info-row">
                    <span><?= t('Points Assigned') ?></span>
                    <strong>
                        <?= $this->text->e(
                            Number_format($kpi['task_point'], 2) ?? 0
                        ) ?>
                    </strong>
                </div>
            </div>

        </div>

    </div>

<?php endif; ?>