<?php
namespace Kanboard\Plugin\KPI\Schema;

const VERSION = 2;

function version_1($pdo)
{
    // KPI Definitions
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_definition (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            user_id INT NOT NULL,
            task_id INT NOT NULL,
            task_point DECIMAL(10,2) DEFAULT 0.00,
            title VARCHAR(150) NOT NULL,
            description TEXT DEFAULT NULL,
            output TEXT NOT NULL,
            target DECIMAL(10,2) DEFAULT 0.00,
            type ENUM('MAJOR', 'MINOR') DEFAULT 'MAJOR',
            actual DECIMAL(10,2) DEFAULT 0.00,
            progress DECIMAL(10,2) DEFAULT 0.00,
            status ENUM('PENDING', 'ONGOING', 'DONE') DEFAULT 'PENDING',
            active TINYINT(1) DEFAULT 1,
            created_at INT NOT NULL,
            updated_at INT NOT NULL,
            timeline_started INT DEFAULT NULL,
            timeline_completed INT DEFAULT NULL,
            target_unit VARCHAR(50) DEFAULT NULL,

            INDEX(project_id)
        ) ENGINE=InnoDB;
    ");

    // KPI Assignments
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_assignment (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kpi_id INT NOT NULL,
            user_id INT NOT NULL,
            project_id INT DEFAULT NULL,
            task_id INT NOT NULL DEFAULT 0,
            task_point DECIMAL(10,2) DEFAULT 0.00,
            is_active TINYINT(1) DEFAULT 1,
            created_at INT NOT NULL,

            INDEX(kpi_id),
            INDEX(user_id),
            INDEX(project_id),
            INDEX(task_id)
        ) ENGINE=InnoDB;
    ");

    // KPI History
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            kpi_id INT NOT NULL,
            actual_value DECIMAL(10,2) DEFAULT NULL,
            target_value DECIMAL(10,2) DEFAULT NULL,
            score DECIMAL(10,2) DEFAULT NULL,
            created_at INT NOT NULL,

            INDEX(project_id),
            INDEX(kpi_id)
        ) ENGINE=InnoDB;
    ");

    // KPI Results
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_result (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kpi_id INT NOT NULL,
            user_id INT NOT NULL,
            actual DECIMAL(10,2) DEFAULT NULL,
            target DECIMAL(10,2) DEFAULT NULL,
            score DECIMAL(10,2) DEFAULT NULL,
            status VARCHAR(20) DEFAULT NULL,
            calculated_at INT DEFAULT NULL,

            INDEX(kpi_id),
            INDEX(user_id)
        ) ENGINE=InnoDB;
    ");

    // User KPI Scores
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_user_score (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT DEFAULT NULL,
            user_id INT DEFAULT NULL,
            score DECIMAL(10,2) DEFAULT NULL,
            completed_tasks INT DEFAULT NULL,
            overdue_tasks INT DEFAULT NULL,
            created_at INT DEFAULT NULL,

            INDEX(project_id),
            INDEX(user_id)
        ) ENGINE=InnoDB;
    ");
}

function version_2($pdo)
{
    // Add missing columns for existing installations
    $pdo->exec("ALTER TABLE kpi_definition ADD COLUMN progress DECIMAL(10,2) DEFAULT 0.00 AFTER actual");
    $pdo->exec("ALTER TABLE kpi_definition ADD COLUMN timeline_started INT DEFAULT NULL");
    $pdo->exec("ALTER TABLE kpi_definition ADD COLUMN timeline_completed INT DEFAULT NULL");
    $pdo->exec("ALTER TABLE kpi_definition ADD COLUMN target_unit VARCHAR(50) DEFAULT NULL");
    $pdo->exec("ALTER TABLE kpi_assignment ADD COLUMN task_id INT NOT NULL DEFAULT 0");
    $pdo->exec("ALTER TABLE kpi_assignment ADD COLUMN task_point DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE kpi_assignment ADD COLUMN is_active TINYINT(1) DEFAULT 1");

    // Funder table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kpi_funder (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_name VARCHAR(255) NOT NULL,
            funder_name VARCHAR(255) NOT NULL,
            project_alias VARCHAR(255) DEFAULT NULL,
            department_name VARCHAR(255) DEFAULT NULL,
            project_funder VARCHAR(255) DEFAULT NULL,
            date_started INT DEFAULT NULL,
            date_completed INT DEFAULT NULL,
            created_at INT NOT NULL DEFAULT 0,
            updated_at INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}
