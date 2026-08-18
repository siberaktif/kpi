<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class KPIController extends BaseController
{
    public function view()
    {
        $taskId = $this->request->getIntegerParam('task_id');
        $kpi    = $this->kpiModel->getKpiAssignTasks($taskId);

        $this->response->html(
            $this->template->render('KPI:kpi/view', [
                'kpi' => $kpi,
            ])
        );
    }

    public function index()
    {
        $project = $this->getProject();

        $getTask = function ($kpi_id) {
            return $this->db
                ->table('kpi_assignment')
                ->left('kpi_definition', 'kd', 'id', 'kpi_assignment', 'kpi_id')
                ->eq('kpi_assignment.kpi_id', $kpi_id)
                ->findAll();
        };

        $kpis = $this->db
            ->table('kpi_definition')
            ->columns(
                'kpi_definition.*',
                'DATE(FROM_UNIXTIME(timeline_started)) AS timeline_start',
                'DATE(FROM_UNIXTIME(timeline_completed)) AS timeline_complete'
            )
            ->eq('project_id', $project['id'])
            ->asc('title')
            ->findAll();

        $project  = $this->getProject();
        $projects = $this->projectModel->getAll();

        $this->response->html(
            $this->helper->layout->app(
                'KPI:kpi/index',
                [
                    'project'  => $project,
                    'projects' => $projects,
                    'kpis'     => $kpis,
                    'getTask'  => $getTask,
                    'title'    => 'Manage KPI > ' . $project['name'],
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
                'values'        => [
                    'project_id' => $project['id'],
                    'target'     => 0,
                    'actual'     => 0,
                    'task_point' => 0,
                ],
                'errors'        => [],
                'taskOptions'   => $this->kpiFormHelper->selectOptionBuilder('tasks', 'id', 'title'),
                'funderOptions' => $this->kpiFormHelper->selectOptionBuilder('kpi_funder', 'id', 'project_alias'),
                'project'       => $project,
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
            return $this->response->html(
                $this->template->render(
                    'KPI:kpi/create',
                    [
                        'values'      => $values,
                        'errors'      => $errors,
                        'taskOptions' => $this->kpiFormHelper->selectOptionBuilder('tasks', 'id', 'title'),
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

        $values['active'] = $values['status'] === 'DONE' ? 0 : 1;

        /*
        * Timestamps.
        */
        $now = time();

        $values['created_at'] = $now;
        $values['updated_at'] = $now;

        $taskId    = $values['task_id'];
        $taskPoint = $values['task_point'];

        unset($values['task_id']);
        unset($values['task_point']);

        /*
        * Save.
        */
        $this->db
            ->table('kpi_definition')
            ->insert($values);

        $kpiId = $this->db->getLastId();

        if ($taskId && $taskPoint) {
            $this->kpiModel->create(
                $taskId,
                $kpiId,
                $taskPoint,
            );
        }

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
        $id        = $this->request->getIntegerParam('id');
        $taskId    = $this->request->getIntegerParam('task_id');
        $taskPoint = (float) $this->request->getStringParam('task_point');

        // Get KPI
        $kpi = $this->db
            ->table('kpi_definition')
            ->columns(
                'kpi_definition.*',
                'p.id AS project_id',
                'p.name AS project_name'
            )
            ->left('projects', 'p', 'id', 'kpi_definition', 'project_id')
            ->eq('kpi_definition.id', $id)
            ->findOne();

        if (! $kpi) {throw new \RuntimeException('KPI not found');}

        // Get project
        $project = $this->projectModel->getById((int) $kpi['project_id']);

        if (! $project) {throw new \RuntimeException('Project not found');}

        // Convert Unix timestamps to HTML date format
        $kpi['timeline_started']   = ! empty($kpi['timeline_started']) ? date('Y-m-d', (int) $kpi['timeline_started']) : '0';
        $kpi['timeline_completed'] = ! empty($kpi['timeline_completed']) ? date('Y-m-d', (int) $kpi['timeline_completed']) : '0';

        // Render edit form
        return $this->response->html(
            $this->template->render(
                'KPI:kpi/edit',
                [
                    'values'        => $kpi,
                    'taskId'        => $taskId,
                    'taskPoint'     => $taskPoint,
                    'errors'        => [],
                    'taskOptions'   => $this->kpiFormHelper->selectOptionBuilder('tasks', 'id', 'title'),
                    'funderOptions' => $this->kpiFormHelper->selectOptionBuilder('kpi_funder', 'id', 'project_alias'),
                    'project'       => $project,
                ]
            )
        );
    }

    public function update()
    {
        $id = $this->request->getIntegerParam('id');

        $values = $this->request->getValues();
        $taskId = $values['task_id'];

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
        $kd_data = [
            'project_id'         => (int) $values['project_id'],
            'title'              => trim($values['title']),
            'description'        => $values['description'] ?? '',
            'output'             => $values['output'] ?? '',
            'type'               => $values['type'] ?? '',
            'target_unit'        => $values['target_unit'] ?? '',
            'target'             => (float) ($values['target'] ?? 0),
            'actual'             => (float) ($values['actual'] ?? 0),
            'status'             => $values['status'] ?? '',
            'timeline_started'   => $timelineStarted,
            'timeline_completed' => $timelineCompleted,
            'updated_at'         => time(),
            'funder_id'          => empty($values['funder_id']) ? null : (int) $values['funder_id'],
            'active'             => $values['status'] === 'DONE' ? 0 : 1
        ];

        $ka_data = [
            'task_point' => (float) $values['task_point'],
            'kpi_id'     => (int) $values['kpi_id'],
        ];

        /*
        * Update
        */
        //kpi_definition
        $this->db
            ->table('kpi_definition')
            ->eq('id', $id)
            ->update($kd_data);

        //kpi_assignment
        $this->db
            ->table('kpi_assignment')
            ->eq('task_id', $taskId)
            ->update($ka_data);

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
