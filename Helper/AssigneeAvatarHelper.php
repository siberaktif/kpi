<?php

namespace Kanboard\Plugin\KPI\Helper;

use Kanboard\Helper\AvatarHelper;

class AssigneeAvatarHelper extends AvatarHelper
{
    /**
     * Optional group_assign model.
     *
     * null when group_assign is not installed.
     */
    private $multiselectMemberModel = null;

    /**
     * Set the optional multiselect member model.
     */
    public function setMultiselectMemberModel($model): void
    {
        $this->multiselectMemberModel = $model;
    }

    /**
     * Render primary + additional assignees.
     */
    public function renderAssignees(
        ?int $assigneeId,
        $ownerMs = null,
        string $css = 'avatar-inline',
        int $size = 20
    ): string {
        $html = '';
        $users = [];

        // Additional assignees
        // Only available when group_assign is installed.
        if (
            !empty($ownerMs) &&
            $this->multiselectMemberModel !== null
        ) {
            $assignees = $this->multiselectMemberModel->getMembers($ownerMs);

            foreach ($assignees as $assignee) {
                $user = $this->userModel->getById($assignee['user_id']);

                if (!empty($user)) {
                    $users[] = $user;
                }
            }
        }

        // Primary assignee — always last
        if (!empty($assigneeId)) {
            $user = $this->userModel->getById($assigneeId);

            if (!empty($user)) {
                $users[] = $user;
            }
        }

        $total = count($users);

        // Maximum 5 visible avatars
        foreach (array_slice($users, 0, 5) as $user) {
            $html .= $this->render(
                $user['id'],
                $user['username'],
                $user['name'],
                $user['email'],
                $user['avatar_path'] ?? '',
                $css,
                $size
            );
        }

        // Show remaining count
        if ($total > 5) {
            $remaining = $total - 5;

            $html .= sprintf(
                '<span class="avatar-inline avatar-more" title="%d more assignees">+%d</span>',
                $remaining,
                $remaining
            );
        }

        return $html;
    }

    /**
     * Render only additional assignees.
     *
     * Returns empty string when group_assign is unavailable.
     */
    public function renderMultiple(
        $ownerMs,
        string $css = 'avatar-inline',
        int $size = 20
    ): string {
        if (
            empty($ownerMs) ||
            $this->multiselectMemberModel === null
        ) {
            return '';
        }

        $assignees = $this->multiselectMemberModel->getMembers($ownerMs);

        $html = '';

        foreach ($assignees as $assignee) {
            $user = $this->userModel->getById(
                $assignee['user_id']
            );

            if (empty($user)) {
                continue;
            }

            $html .= $this->render(
                $user['id'],
                $user['username'],
                $user['name'],
                $user['email'],
                $user['avatar_path'] ?? '',
                $css,
                $size
            );
        }

        return $html;
    }
}