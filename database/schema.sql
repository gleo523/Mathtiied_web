CREATE DATABASE IF NOT EXISTS mathtified_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mathtified_db;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(120) NOT NULL UNIQUE,
  email VARCHAR(190) NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student',
  first_name VARCHAR(80) NULL,
  last_name VARCHAR(80) NULL,
  section VARCHAR(120) NULL,
  school_number VARCHAR(80) NULL UNIQUE,
  last_level INT UNSIGNED NOT NULL DEFAULT 1,
  saved_m_value DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
  has_unfinished_game TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS game_sessions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  external_id VARCHAR(190) NULL,
  topic VARCHAR(190) NULL,
  level INT UNSIGNED NULL,
  mastery_value DECIMAL(10,4) NULL,
  score DECIMAL(10,4) NULL,
  is_struggling TINYINT(1) NOT NULL DEFAULT 0,
  started_at DATETIME NULL,
  ended_at DATETIME NULL,
  last_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  raw_payload JSON NULL,
  UNIQUE KEY uq_game_session_external (user_id, external_id),
  KEY idx_game_sessions_user_seen (user_id, last_seen_at),
  CONSTRAINT fk_game_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS game_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  session_id BIGINT UNSIGNED NULL,
  topic VARCHAR(190) NOT NULL,
  assessment VARCHAR(190) NULL,
  score DECIMAL(10,4) NOT NULL,
  max_score DECIMAL(10,4) NOT NULL DEFAULT 100,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  raw_payload JSON NULL,
  KEY idx_attempts_user_date (user_id, attempted_at),
  CONSTRAINT fk_attempts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_attempts_session FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS topic_mastery (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  topic VARCHAR(190) NOT NULL,
  mastery DECIMAL(6,3) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_topic_mastery (user_id, topic),
  CONSTRAINT fk_topic_mastery_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  event_type VARCHAR(80) NOT NULL,
  title VARCHAR(190) NOT NULL,
  description TEXT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  raw_payload JSON NULL,
  KEY idx_activity_user_date (user_id, occurred_at),
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS review_modules (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  teacher_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(190) NOT NULL,
  topic VARCHAR(190) NOT NULL,
  instructions TEXT NULL,
  status ENUM('draft', 'pending', 'published', 'completed') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  KEY idx_review_modules_teacher (teacher_id),
  CONSTRAINT fk_review_modules_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS review_module_assignments (
  module_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  status ENUM('assigned', 'in_progress', 'completed') NOT NULL DEFAULT 'assigned',
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  PRIMARY KEY (module_id, user_id),
  CONSTRAINT fk_assignment_module FOREIGN KEY (module_id) REFERENCES review_modules(id) ON DELETE CASCADE,
  CONSTRAINT fk_assignment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE review_modules
  ADD COLUMN IF NOT EXISTS teacher_id BIGINT UNSIGNED NULL,
  ADD COLUMN IF NOT EXISTS instructions TEXT NULL,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS completed_at DATETIME NULL,
  ADD KEY IF NOT EXISTS idx_review_modules_teacher (teacher_id);

ALTER TABLE review_module_assignments
  ADD COLUMN IF NOT EXISTS status ENUM('assigned', 'in_progress', 'completed') NOT NULL DEFAULT 'assigned',
  ADD COLUMN IF NOT EXISTS completed_at DATETIME NULL;

CREATE TABLE IF NOT EXISTS wave_scores (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  topic VARCHAR(190) NOT NULL,
  wave_number INT UNSIGNED NOT NULL,
  score DECIMAL(10,4) NOT NULL DEFAULT 0,
  recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_wave_scores_user_topic (user_id, topic, recorded_at),
  CONSTRAINT fk_wave_scores_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS topic_content (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  part_id VARCHAR(80) NOT NULL,
  content_type ENUM('questions', 'explanations', 'why_check') NOT NULL,
  content_json JSON NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_topic_content (part_id, content_type),
  KEY idx_topic_content_part_type (part_id, content_type)
);

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS teacher_id BIGINT UNSIGNED NULL,
  ADD KEY IF NOT EXISTS idx_users_teacher (teacher_id);

CREATE TABLE IF NOT EXISTS system_settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value VARCHAR(255) NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO system_settings (setting_key, setting_value)
VALUES ('dwti_review_threshold', '0.70')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

CREATE TABLE IF NOT EXISTS teacher_auth_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  teacher_id BIGINT UNSIGNED NOT NULL,
  event_type ENUM('login', 'logout') NOT NULL,
  ip_address VARCHAR(45) NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_teacher_auth_logs_date (teacher_id, occurred_at),
  CONSTRAINT fk_teacher_auth_logs_user FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS teacher_profiles (
  user_id BIGINT UNSIGNED PRIMARY KEY,
  phone VARCHAR(40) NULL,
  school VARCHAR(190) NULL,
  department VARCHAR(190) NULL,
  grade_level VARCHAR(80) NULL,
  sections VARCHAR(500) NULL,
  email_notifications TINYINT(1) NOT NULL DEFAULT 1,
  progress_summary TINYINT(1) NOT NULL DEFAULT 1,
  profile_photo VARCHAR(255) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_teacher_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
