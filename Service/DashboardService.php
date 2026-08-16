<?php
namespace Kanboard\Plugin\KPI\Service;

use Kanboard\Core\Base;

class DashboardService extends Base
{
    private function parseSearchDate(string $search): ?array
    {
        $search = trim($search);

        if ($search === '') {
            return null;
        }

        $currentYear = date('Y');

        /*
        * ---------------------------------------------------------
        * YYYY-MM-DD
        * Example: 2026-07-29
        * ---------------------------------------------------------
        */
        if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $search)) {

            $timestamp = strtotime($search);

            if ($timestamp !== false) {
                return [
                    'start' => strtotime(date('Y-m-d 00:00:00', $timestamp)),
                    'end'   => strtotime(date('Y-m-d 23:59:59', $timestamp)),
                ];
            }
        }

        /*
        * ---------------------------------------------------------
        * MM/DD/YYYY
        * Example: 07/29/2026
        * ---------------------------------------------------------
        */
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $search)) {

            $timestamp = strtotime($search);

            if ($timestamp !== false) {
                return [
                    'start' => strtotime(date('Y-m-d 00:00:00', $timestamp)),
                    'end'   => strtotime(date('Y-m-d 23:59:59', $timestamp)),
                ];
            }
        }

        /*
        * ---------------------------------------------------------
        * Month Day, Year
        * Example: July 29, 2026
        * ---------------------------------------------------------
        */
        if (preg_match(
            '/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{1,2},\s*\d{4}$/i',
            $search
        )) {

            // Remove comma
            $dateString = str_replace(',', '', $search);

            $timestamp = strtotime($dateString);

            if ($timestamp !== false) {
                return [
                    'start' => strtotime(date('Y-m-d 00:00:00', $timestamp)),
                    'end'   => strtotime(date('Y-m-d 23:59:59', $timestamp)),
                ];
            }
        }

        /*
        * ---------------------------------------------------------
        * Month Day
        * Example: July 29
        *
        * Uses current year.
        * ---------------------------------------------------------
        */
        if (preg_match(
            '/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{1,2}$/i',
            $search
        )) {

            $timestamp = strtotime($search . ' ' . $currentYear);

            if ($timestamp !== false) {
                return [
                    'start' => strtotime(date('Y-m-d 00:00:00', $timestamp)),
                    'end'   => strtotime(date('Y-m-d 23:59:59', $timestamp)),
                ];
            }
        }

        /*
        * ---------------------------------------------------------
        * Month Year
        * Example: July 2026
        * ---------------------------------------------------------
        */
        if (preg_match(
            '/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{4}$/i',
            $search
        )) {

            $timestamp = strtotime('1 ' . $search);

            if ($timestamp !== false) {

                $dateStart = strtotime(date('Y-m-01 00:00:00', $timestamp));
                $dateEnd   = strtotime(date('Y-m-t 23:59:59', $timestamp));

                return [
                    'start' => $dateStart,
                    'end'   => $dateEnd,
                ];
            }
        }

        /*
        * ---------------------------------------------------------
        * Month only
        * Example: July
        *
        * Uses current year.
        * ---------------------------------------------------------
        */
        if (preg_match(
            '/^(January|February|March|April|May|June|July|August|September|October|November|December)$/i',
            $search
        )) {

            $timestamp = strtotime('1 ' . $search . ' ' . $currentYear);

            if ($timestamp !== false) {

                $dateStart = strtotime(date('Y-m-01 00:00:00', $timestamp));
                $dateEnd   = strtotime(date('Y-m-t 23:59:59', $timestamp));

                return [
                    'start' => $dateStart,
                    'end'   => $dateEnd,
                ];
            }
        }

        return null;
    }

    public function getProjectStats($projectId)
    {
        $completed = $this->db->table('tasks')
            ->eq('project_id', $projectId)
            ->eq('is_active', 0)
            ->count();

        $open = $this->db->table('tasks')
            ->eq('project_id', $projectId)
            ->eq('is_active', 1)
            ->count();

        $overdue = $this->db->table('tasks')
            ->eq('project_id', $projectId)
            ->eq('is_active', 1)
            ->lt('date_due', time())
            ->count();

        $total = $completed + $open;

        $progress = $total > 0
            ? round(($completed / $total) * 100, 1)
            : 0;

        return [
            'completed' => $completed,
            'open'      => $open,
            'overdue'   => $overdue,
            'total'     => $total,
            'progress'  => $progress,
        ];
    }

    public function getProjectTable(
        int $projectId,
        ?string $status = null,
        int $page = 1,
        int $limit = 10,
        string $sort = 'date_started',
        string $direction = 'desc'
    ): array {
        $search = trim($this->request->getStringParam('search'));
        $sort   = $this->request->getStringParam('sort', 'date_started');

        $direction = strtolower($this->request->getStringParam('direction', 'desc'));

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $offset = max(0, ($page - 1) * $limit);

        $sql = "
            SELECT
                t.id,
                t.title,
                t.description,
                t.date_completed,
                t.date_started,
                t.date_due,
                t.column_id,
                t.owner_ms,
                u.id AS assignee_id,
                u.username AS assignee_username,
                u.name AS assignee_name,
                u.email AS assignee_email,
                c.title AS column_name,
                co.comment_count
            FROM tasks t
            LEFT JOIN users u
                ON u.id = t.owner_id
            LEFT JOIN columns c
                ON c.id = t.column_id
            LEFT JOIN (
                SELECT task_id, COUNT(*) AS comment_count
                FROM comments
                GROUP BY task_id
            ) co ON co.task_id = t.id
            WHERE t.project_id = ?
        ";

        $params = [$projectId];

        switch ($status) {

            case 'completed':
                $sql .= " AND t.is_active = 0";
                break;

            case 'open':
                $sql .= " AND t.is_active = 1";
                break;

            case 'overdue':
                $sql .= "
                AND t.is_active = 1
                AND t.date_due > 0
                AND t.date_due < ?
            ";

                $params[] = time();
                break;

            default:
                return [];
        }

        /*
        * SEARCH
        */
        if (str_starts_with($search, 'KPI:')) {
            $search    = trim(substr($search, 4));
            $dateRange = $this->parseSearchDate($search);

            if ($dateRange !== null) {

                $searchDate = date('Y-m-d', $dateRange['start']);

                $sql .= "
                    AND (
                        DATE(FROM_UNIXTIME(t.date_started)) = ?
                        OR
                        DATE(FROM_UNIXTIME(t.date_due)) = ?
                    )
                ";

                $params[] = $searchDate;
                $params[] = $searchDate;

            } else {

                // Normal text search
                $sql .= "
                    AND (
                        t.title LIKE ?
                        OR t.description LIKE ?
                        OR u.username LIKE ?
                        OR c.title LIKE ?
                    )
                ";

                $searchParam = '%' . $search . '%';

                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
        }

        $sortColumns = [
            'title'        => 't.title',
            'assignee'     => 'u.username',
            'date_started' => 't.date_started',
            'date_due'     => 't.date_due',
            'comments'     => 'co.comment_count',
            'column'       => 'c.title',
        ];

        $orderBy = $sortColumns[$sort] ?? 't.date_started';

        $orderDirection = strtolower($direction) === 'desc'
            ? 'DESC'
            : 'ASC';

        $sql .= sprintf(
            ' ORDER BY %s %s LIMIT %d OFFSET %d',
            $orderBy,
            $orderDirection,
            (int) $limit,
            (int) $offset
        );

        return $this->db->execute($sql, $params)->fetchAll();
    }

    public function countProjectTable(
        int $projectId,
        ?string $status = null
    ): int {
        $search = trim($this->request->getStringParam('search'));

        $sql = "
        SELECT COUNT(DISTINCT t.id)
        FROM tasks t

        LEFT JOIN users u
            ON u.id = t.owner_id

        LEFT JOIN columns c
            ON c.id = t.column_id

        WHERE t.project_id = ?
    ";

        $params = [$projectId];

        /*
     * STATUS
     */
        switch ($status) {

            case 'completed':
                $sql .= " AND t.is_active = 0";
                break;

            case 'open':
                $sql .= " AND t.is_active = 1";
                break;

            case 'overdue':
                $sql .= "
                AND t.is_active = 1
                AND t.date_due > 0
                AND t.date_due < ?
            ";

                $params[] = time();
                break;

            default:
                return 0;
        }

        /*
     * SEARCH
     */
        if (str_starts_with($search, 'KPI:')) {

            $search = trim(substr($search, 4));

            $dateRange = $this->parseSearchDate($search);

            if ($dateRange !== null) {

                $searchDate = date(
                    'Y-m-d',
                    $dateRange['start']
                );

                $sql .= "
                AND (
                    DATE(FROM_UNIXTIME(t.date_started)) = ?
                    OR
                    DATE(FROM_UNIXTIME(t.date_due)) = ?
                )
            ";

                $params[] = $searchDate;
                $params[] = $searchDate;

            } else {

                $sql .= "
                AND (
                    t.title LIKE ?
                    OR t.description LIKE ?
                    OR u.username LIKE ?
                    OR c.title LIKE ?
                )
            ";

                $searchParam = '%' . $search . '%';

                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
        }

        return (int) $this->db
            ->execute($sql, $params)
            ->fetchColumn();
    }

    public function getTaskList(int $projectId, int | array $ids = [])
    {
        $query = $this->db
            ->table('tasks')
            ->columns(
                'tasks.id', 
                'tasks.title', 
                'tasks.description', 
                'tasks.date_started', 
                'tasks.date_due',
                'c.title AS column_name',
                'ka.kpi_id',
                'ka.task_point'
                )
            ->left('kpi_assignment', 'ka', 'task_id', 'tasks', 'id')
            ->left('columns', 'c', 'id', 'tasks', 'column_id')
            ->eq('tasks.project_id', $projectId);

        if (is_int($ids) && $ids > 0) {
            return $query
                ->eq('tasks.id', $ids)
                ->findOne();
        }

        if (is_array($ids) && ! empty($ids)) {
            return $query
                ->in('ka.task_id', $ids)
                ->findAll();
        }

        return $query->findAll();
    }

    public function getKpiAssignmentTasksIds(int $kpi_id): array
    {
        $rows = $this->db
            ->table('kpi_assignment')
            ->columns('task_id')
            ->eq('kpi_id', $kpi_id)
            ->findAll();

        return array_column($rows, 'task_id');
    }

    public function getColumnTaskList(int $projectId, int $taskId = 0)
    {
        $query = $this->db
            ->table('tasks')
            ->columns('tasks.*', 'c.title AS column_name')
            ->left('columns', 'c', 'id', 'tasks', 'column_id')
            ->eq('c.project_id', $projectId);

        if ($taskId > 0) {
            return $query
                ->eq('tasks.id', $taskId)
                ->findOne();
        }

        return $query->findAll();
    }

    public function getProjectList(int $projectId = 0)
    {
        $query = $this->db
            ->table('projects')
            ->columns('id', 'name');

        if ($projectId > 0) {
            return $query
                ->eq('id', $projectId)
                ->findOne();
        }

        return $query->findAll();
    }

    public function getTaskStatusChart($projectId)
    {
        $stats = $this->getProjectStats($projectId);

        return [
            $stats['completed'],
            $stats['open'],
            $stats['overdue'],
        ];
    }

    public function getKpiStats($projectId)
    {
        // Get KPI status counts
        $counts = $this->db
            ->table('kpi_definition')
            ->columns('status', 'COUNT(*) AS total')
            ->eq('project_id', $projectId)
            ->groupBy('status')
            ->findAll();

        $kpiStats = [
            'done'      => 0,
            'ongoing'   => 0,
            'pending'   => 0,
            'scheduled' => 0,
            'planned'   => 0,
            'kpiProg'   => 0,
        ];

        $total_kpi = 0;

        foreach ($counts as $row) {

            $count      = (int) $row['total'];
            $total_kpi += $count;

            switch (strtoupper($row['status'])) {

                case 'DONE':
                    $kpiStats['done'] = $count;
                    break;

                case 'ONGOING':
                    $kpiStats['ongoing'] = $count;
                    break;

                case 'PENDING':
                    $kpiStats['pending'] = $count;
                    break;

                case 'SCHEDULED':
                    $kpiStats['scheduled'] = $count;
                    break;

                case 'PLANNED':
                    $kpiStats['planned'] = $count;
                    break;
            }
        }

        // Calculate overall progress
        if ($total_kpi > 0) {
            $kpiStats['kpiProg'] = round(($kpiStats['done'] / $total_kpi) * 100, 2);
        }

        return $kpiStats;
    }

    public function getTaskTrend($projectId)
    {
        $tasks = $this->db
            ->table('tasks')
            ->columns(
                'date_creation',
                'date_completed'
            )
            ->eq('project_id', $projectId)
            ->orderBy('date_creation')
            ->findAll();

        $months = [];

        foreach ($tasks as $task) {

            // Group by the month the task was created
            $month = date('Y-m', $task['date_creation']);

            if (! isset($months[$month])) {
                $months[$month] = [
                    'total'     => 0,
                    'completed' => 0,
                ];
            }

            // Total tasks created this month
            $months[$month]['total']++;

            // Task is completed
            if (
                ! empty($task['date_completed']) &&
                $task['date_completed'] > 0
            ) {
                $months[$month]['completed']++;
            }
        }

        $result = [
            'labels'     => [],
            'total'      => [],
            'completed'  => [],
            'percentage' => [],
        ];

        foreach ($months as $month => $values) {

            $total     = $values['total'];
            $completed = $values['completed'];

            $percentage = $total > 0
                ? round(($completed / $total) * 100, 2)
                : 0;

            $result['labels'][] = date(
                'M Y',
                strtotime($month . '-01')
            );

            $result['total'][]      = $total;
            $result['completed'][]  = $completed;
            $result['percentage'][] = $percentage;
        }

        return $result;
    }
}
