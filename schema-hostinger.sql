-- Sadaf Al Bahr — import this file on Hostinger
-- 1. hPanel → Databases → create a MySQL database
-- 2. phpMyAdmin → select THAT database (do not create another)
-- 3. Import this file
-- Extra tables (projects, blog, SEO, catalogue) are also created automatically when the site first loads.

CREATE TABLE IF NOT EXISTS inquiries (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  phone      VARCHAR(40)  NOT NULL,
  service    VARCHAR(80)  NOT NULL DEFAULT '',
  message    TEXT         NOT NULL,
  ip_address VARCHAR(45)  DEFAULT NULL,
  is_read    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inquiries_created (created_at),
  INDEX idx_inquiries_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(60)  NOT NULL UNIQUE,
  title       VARCHAR(160) NOT NULL,
  summary     TEXT         NOT NULL,
  bullets     TEXT         NOT NULL,
  image_url   VARCHAR(500) NOT NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  image_url  VARCHAR(500) NOT NULL,
  caption    VARCHAR(160) NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
