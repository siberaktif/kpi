<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class KPIController extends BaseController
{
    public function index()
    {

        $project = $this->getProject();

        $kpis = $this->db
            ->table('kpi_definition')
            ->columns(
                'kpi_definition.*',
                't.title AS task_name',
                'DATE(FROM_UNIXTIME(kpi_definition.timeline_started)) AS timeline_start',
                'DATE(FROM_UNIXTIME(kpi_definition.timeline_completed)) AS timeline_complete'
            )
            ->eq('kpi_definition.project_id', $project['id'])
            ->left('tasks', 't', 'id', 'kpi_definition', 'task_id')
            ->asc('kpi_definition.title')
            ->findAll();

        $project  = $this->getProject();
        $projects = $this->projectModel->getAll();

        $this->response->html(
            $this->helper->layout->app(
                'KPI:kpi/index',
                [
                    'project'     => $project,
                    'projects'    => $projects,
                    'kpis'        => $kpis,
                    'title'       => $project['name'],
                    'description' => $this->helper->projectHeader->getDescription($project),
                ]
            )
        );
    }

    public function project()
    {
        $project  = $this->getProject();
        $projects = $this->projectModel->getAll();

        $stats     = $this->dashboardService->getProjectStats($project['id']);
        $kpiStats  = $this->dashboardService->getKpiStats($project['id']);
        $taskTrend = $this->dashboardService->getTaskTrend($project['id']);

        $this->response->html(
            $this->helper->layout->app(
                'KPI:project/index',
                [
                    'project'     => $project,
                    'projects'    => $projects,
                    'stats'       => $stats,
                    'kpiStats'    => $kpiStats,
                    'taskTrend'   => $taskTrend,
                    'title'       => $project['name'],
                    'description' => $this->helper->projectHeader->getDescription($project),
                ]
            )
        );
    }

    private function getTaskOptions(int $projectId): array
    {
        $tasks = $this->dashboardService->getTaskList($projectId);

        $options = [
            0 => t('Select a task'),
        ];

        foreach ($tasks as $task) {
            $options[$task['id']] = $task['title'];
        }

        return $options;
    }

    public function create()
    {
        $project = $this->getProject();

        $this->response->html(
            $this->template->render('KPI:kpi/create', [
                'values'      => [
                    'project_id' => $project['id'],
                    'target'     => 0,
                    'actual'     => 0,
                    'task_point' => 0,
                ],
                'errors'      => [],
                'taskOptions' => $this->getTaskOptions($project['id']),
                'project'     => $project,
            ])
        );
    }

    public function save()
    {
        $values = $this->request->getValues();
        $errors = [];

        // Validate Timeline Started
        if (empty($values['title'])) {
            $errors['title'][] = t(
                'Title is required.'
            );
        }

        // Validate date relationship
        if (
            ! empty($values['timeline_started']) &&
            ! empty($values['timeline_completed'])
        ) {
            $started   = strtotime($values['timeline_started']);
            $completed = strtotime($values['timeline_completed']);

            if ($completed < $started) {
                $errors['timeline_completed'][] = t(
                    'Timeline completed must be after timeline started.'
                );
            }
        }

        /*
        * Stop here if validation failed.
        */
        if (! empty($errors)) {
            // $this->flash->failure(
            //     t('Please correct the errors below.')
            // );

            return $this->response->html(
                $this->template->render(
                    'KPI:kpi/create',
                    [
                        'values'      => $values,
                        'errors'      => $errors,
                        'taskOptions' => $this->getTaskOptions(
                            (int) $values['project_id']
                        ),
                        'project'     => $this->projectModel->getById((int) $values['project_id']),
                    ]
                )
            );
        }

        /*
        * Convert dates to Unix timestamps
        * only after validation succeeds.
        */
        $values['timeline_started'] = strtotime(
            $values['timeline_started'] . ' 00:00:00'
        );

        $values['timeline_completed'] = strtotime(
            $values['timeline_completed'] . ' 00:00:00'
        );

        /*
        * Timestamps.
        */
        $now = time();

        $values['created_at'] = $now;
        $values['updated_at'] = $now;

        /*
        * Save.
        */
        $this->db
            ->table('kpi_definition')
            ->insert($values);

        $this->flash->success(
            t('KPI created successfully.')
        );

        return $this->response->redirect(
            $this->helper->url->to(
                'KPIController',
                'index',
                []
            ),
            true
        );
    }

    public function edit()
    {
        $id = $this->request->getIntegerParam('id');

        // Get KPI
        $kpi = $this->db
            ->table('kpi_definition')
            ->columns(
                'kpi_definition.*',
                'p.id AS project_id',
                'p.name AS project_name'
            )
            ->left(
                'projects',
                'p',
                'id',
                'kpi_definition',
                'project_id'
            )
            ->eq('kpi_definition.id', $id)
            ->findOne();

        if (! $kpi) {
            throw new \RuntimeException('KPI not found');
        }

        // Get project
        $project = $this->projectModel->getById(
            (int) $kpi['project_id']
        );

        if (! $project) {
            throw new \RuntimeException('Project not found');
        }

        // Get task options for this project
        $taskOptions = $this->getTaskOptions(
            (int) $kpi['project_id']
        );

        // Convert Unix timestamps to HTML date format
        $kpi['timeline_started'] = ! empty($kpi['timeline_started'])
            ? date('Y-m-d', (int) $kpi['timeline_started'])
            : '0';

        $kpi['timeline_completed'] = ! empty($kpi['timeline_completed'])
            ? date('Y-m-d', (int) $kpi['timeline_completed'])
            : '0';

        // Render edit form
        return $this->response->html(
            $this->template->render(
                'KPI:kpi/edit',
                [
                    'values'      => $kpi,
                    'errors'      => [],
                    'taskOptions' => $taskOptions,
                    'project'     => $project,
                ]
            )
        );
    }

    public function update()
    {
        $id = $this->request->getIntegerParam('id');

        $values = $this->request->getValues();
        $errors = [];

        /*
        * Validate ID
        */
        if ($id <= 0) {
            throw new \RuntimeException('Invalid KPI ID.');
        }

        /*
        * Validate Title
        */
        if (empty($values['title'])) {
            $errors['title'][] = t(
                'Title is required.'
            );
        }

        
        /*
        * Validate date relationship
        */
        if (
            ! empty($values['timeline_started']) &&
            ! empty($values['timeline_completed'])
        ) {
            $started   = strtotime($values['timeline_started']);
            $completed = strtotime($values['timeline_completed']);

            if ($completed < $started) {
                $errors['timeline_completed'][] = t(
                    'Timeline completed must be after timeline started.'
                );
            }
        }

        /*
        * Validation failed
        */
        if (! empty($errors)) {

            $projectId = (int) ($values['project_id'] ?? 0);

            $project = $projectId > 0
                ? $this->projectModel->getById($projectId)
                : null;

            return $this->response->html(
                $this->template->render(
                    'KPI:kpi/edit',
                    [
                        'values'      => array_merge(
                            $values,
                            ['id' => $id]
                        ),
                        'errors'      => $errors,
                        'taskOptions' => $this->getTaskOptions(
                            $projectId
                        ),
                        'project'     => $project,
                    ]
                )
            );
        }

        /*
        * Convert dates to Unix timestamps
        */
        $timelineStarted = strtotime(
            $values['timeline_started'] . ' 00:00:00'
        );

        $timelineCompleted = strtotime(
            $values['timeline_completed'] . ' 00:00:00'
        );

        /*
        * Prepare update data
        */
        $data = [
            'project_id'         => (int) $values['project_id'],
            'task_id'            => (int) $values['task_id'],
            'title'              => trim($values['title']),
            'description'        => $values['description'] ?? '',
            'output'             => $values['output'] ?? '',
            'type'               => $values['type'] ?? '',
            'target_unit'        => $values['target_unit'] ?? '',
            'target'             => (float) ($values['target'] ?? 0),
            'actual'             => (float) ($values['actual'] ?? 0),
            'status'             => $values['status'] ?? '',
            'task_point'         => (float) $values['task_point'],
            'timeline_started'   => $timelineStarted,
            'timeline_completed' => $timelineCompleted,
            'updated_at'         => time(),
        ];

        /*
        * Update
        */
        $this->db
            ->table('kpi_definition')
            ->eq('id', $id)
            ->update($data);

        $this->flash->success(
            t('KPI updated successfully.')
        );

        return $this->response->redirect(
            $this->helper->url->to(
                'KPIController',
                'index',
                []
            ),
            true
        );
    }

    public function confirm()
    {
        $kpi_id   = $this->request->getIntegerParam('kpi_id');
        $kpi_name = $this->request->getStringParam('kpi_name');

        $this->response->html($this->template->render('KPI:kpi/removed', [
            'kpi_id'   => $kpi_id,
            'kpi_name' => $kpi_name,
        ]));
    }

    public function remove()
    {
        $id = $this->request->getIntegerParam('id');

        $this->db
            ->table('kpi_definition')
            ->eq('id', $id)
            ->remove();

        $this->flash->success(t('KPI deleted successfully.'));

        $this->response->redirect(
            $this->helper->url->to('KPIController', 'index', []), true
        );
    }
}
