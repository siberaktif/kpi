<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Paginator;

class TaskController extends BaseController
{
    private function renderTaskList(
        array $project,
        string $status,
        string $template,
        string $title,
    ): void {
        $limit = 10;
        $sort  = $this->request->getStringParam('sort', 'date_started');

        $direction = strtolower($this->request->getStringParam('direction', 'desc'));

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $total = $this->dashboardService->countProjectTable($project['id'], $status);

        $paginator = new Paginator($this->container);

        $paginator
            ->setTotal($total)
            ->setMax($limit)
            ->setUrl(
                'TaskController',
                $this->request->getStringParam('action'),
                [
                    'project_id' => $project['id'],
                    'plugin'     => 'KPI',
                    'search'     => $this->request->getStringParam('search'),
                    'sort'       => $sort,
                    'direction'  => $direction,
                ]
            )
            ->calculate();

        $page = $paginator->getPage();

        $tasks = $this->dashboardService->getProjectTable(
            $project['id'],
            $status,
            $page,
            $limit,
            $sort,
            $direction
        );

        $this->response->html(
            $this->helper->layout->app(
                $template,
                [
                    'project' => $project,
                    'tasks'   => $tasks,
                    'title'   => "{$title} ($total)",
                    'task_count'            => $total,
                    'page'                  => $page,
                    'limit'                 => $limit,
                    'sort'                  => $sort,
                    'direction'             => $direction,
                    'paginator'             => $paginator,
                    'assigneeAvatarService' =>
                    $this->container['assigneeAvatarService'],
                ]
            )
        );
    }

    public function task_open()
    {
        $this->renderTaskList(
            $this->getProject(),
            'open',
            'KPI:kpi/task_open',
            'Open tasks'
        );
    }

    public function task_overdue()
    {
        $this->renderTaskList(
            $this->getProject(),
            'overdue',
            'KPI:kpi/task_overdue',
            'Overdue tasks'
        );
    }

    public function task_completed()
    {
        $this->renderTaskList(
            $this->getProject(),
            'completed',
            'KPI:kpi/task_complete',
            'Completed tasks'
        );
    }

    public function comments()
    {
        $taskId = $this->request->getIntegerParam('task_id');

        $task = $this->taskFinderModel->getById($taskId);

        if (empty($task)) {
            throw new \Kanboard\Core\Controller\PageNotFoundException(
                t('Task not found.')
            );
        }

        $comments = $this->commentModel->getAll($taskId);

        $this->response->html(
            $this->template->render(
                'KPI:kpi/task_comments',
                [
                    'task'     => $task,
                    'comments' => $comments,
                ]
            )
        );
    }

    public function taskAssign()
    {
        $kpi_id     = $this->request->getIntegerParam('kpi_id');
        $project_id = $this->request->getIntegerParam('project_id');

        $tasksIds = $this->dashboardService->getKpiAssignmentTasksIds($kpi_id);
        $tasks     = $this->dashboardService->getTaskList($project_id, $tasksIds);
        $project  = $this->dashboardService->getProjectList($project_id);

        //print_r($tasks);

        $this->response->html(
            $this->template->render(
                'KPI:kpi/task_assign',
                [
                    'tasks'    => $tasks,
                    'project' => $project,
                ]
            )
        );
    }
}
