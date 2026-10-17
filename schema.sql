-- Sadaf Al Bahr — MySQL schema (XAMPP)
-- Import in phpMyAdmin or: mysql -u root < schema.sql

CREATE DATABASE IF NOT EXISTS sadaf_al_bahr
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sadaf_al_bahr;

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

INSERT INTO services (slug, title, summary, bullets, image_url, sort_order)
SELECT * FROM (
  SELECT
    'wallpaper' AS slug,
    'Wallpaper' AS title,
    'Decorative, vinyl, fabric and 3D wallpapers. We help you choose a pattern that suits the room, then paste and align so seams stay quiet and corners stay sharp.' AS summary,
    'Feature walls and full-room covering\nWallpaper and wall stickers\nSurface preparation and neat trimming' AS bullets,
    'https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=1200&q=80' AS image_url,
    1 AS sort_order
  UNION ALL SELECT
    'carpets',
    'Carpets & flooring',
    'Carpets, rugs, SPC and PVC flooring, and artificial grass. We cut, lay and finish so the floor feels even underfoot and looks complete from the door.',
    'Wall-to-wall carpet and area rugs\nSPC & PVC flooring\nArtificial grass carpet',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1200&q=80',
    2
  UNION ALL SELECT
    'curtains',
    'Curtains, blinds & fabrics',
    'Curtain fabrics, blackout, sheer, eyelet, pinch-pleat and blinds. We stitch to your window and hang the tracks or rods ourselves.',
    'Custom stitching in your chosen fabric\nBlinds and upholstery fabrics\nTracks, rods and dressing',
    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
    3
  UNION ALL SELECT
    'decoration',
    'Decoration',
    'We help compose the room: colour, texture, window treatment and floor together — not as separate products. Bring a photo; we will suggest a practical scheme you can install.',
    '',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80',
    4
  UNION ALL SELECT
    'fixing',
    'Professional fixing',
    'Installation is included in how we work. Our team measures on site, then returns to hang curtains, paste wallpaper and lay carpet so you are not left with rolls and a drill.',
    'Site measure\nClean install\nHardware\nAftercare',
    '',
    5
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM services LIMIT 1);

INSERT INTO gallery (image_url, caption, sort_order)
SELECT * FROM (
  SELECT 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1000&q=80' AS image_url, 'Curtains' AS caption, 1 AS sort_order
  UNION ALL SELECT 'https://images.unsplash.com/photo-1615874959474-d609969a20d7?auto=format&fit=crop&w=1000&q=80', 'Wallpaper', 2
  UNION ALL SELECT 'https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=1000&q=80', 'Carpets', 3
  UNION ALL SELECT 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1000&q=80', 'Living rooms', 4
  UNION ALL SELECT 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1000&q=80', 'Bedrooms', 5
  UNION ALL SELECT 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1000&q=80', 'Decoration', 6
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM gallery LIMIT 1);

CREATE TABLE IF NOT EXISTS projects (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title            VARCHAR(180) NOT NULL,
  slug             VARCHAR(180) NOT NULL UNIQUE,
  category         VARCHAR(40)  NOT NULL DEFAULT 'Installation',
  location         VARCHAR(160) NOT NULL DEFAULT '',
  summary          VARCHAR(300) NOT NULL DEFAULT '',
  description      TEXT         NOT NULL,
  meta_title       VARCHAR(180) NOT NULL DEFAULT '',
  meta_description VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
  is_published     TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order       INT          NOT NULL DEFAULT 0,
  created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_projects_pub (is_published, sort_order, id),
  INDEX idx_projects_cat (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_images (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text   VARCHAR(180) NOT NULL DEFAULT '',
  caption    VARCHAR(180) NOT NULL DEFAULT '',
  sort_order INT          NOT NULL DEFAULT 0,
  CONSTRAINT fk_project_images_project
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_landing (
  id               TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  heading          VARCHAR(180) NOT NULL DEFAULT 'Blog',
  intro            TEXT         NOT NULL,
  cover_image      VARCHAR(255) NOT NULL DEFAULT '',
  cover_alt        VARCHAR(180) NOT NULL DEFAULT '',
  meta_title       VARCHAR(180) NOT NULL DEFAULT '',
  meta_description VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword    VARCHAR(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title            VARCHAR(180) NOT NULL,
  slug             VARCHAR(180) NOT NULL UNIQUE,
  excerpt          VARCHAR(320) NOT NULL DEFAULT '',
  cover_image      VARCHAR(255) NOT NULL DEFAULT '',
  cover_alt        VARCHAR(180) NOT NULL DEFAULT '',
  category         VARCHAR(40)  NOT NULL DEFAULT 'Guides',
  meta_title       VARCHAR(180) NOT NULL DEFAULT '',
  meta_description VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
  is_published     TINYINT(1)   NOT NULL DEFAULT 1,
  published_at     DATE         DEFAULT NULL,
  created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_blog_pub (is_published, published_at, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_sections (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id    INT UNSIGNED NOT NULL,
  heading    VARCHAR(180) NOT NULL DEFAULT '',
  body       TEXT         NOT NULL,
  image_path VARCHAR(255) NOT NULL DEFAULT '',
  image_alt  VARCHAR(180) NOT NULL DEFAULT '',
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT fk_blog_sections_post
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_seo (
  page_key         VARCHAR(40)  NOT NULL PRIMARY KEY,
  label            VARCHAR(80)  NOT NULL,
  php_file         VARCHAR(80)  NOT NULL,
  slug             VARCHAR(180) NOT NULL DEFAULT '',
  meta_title       VARCHAR(180) NOT NULL DEFAULT '',
  meta_description VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword    VARCHAR(120) NOT NULL DEFAULT '',
  robots           VARCHAR(40)  NOT NULL DEFAULT 'index,follow',
  og_image         VARCHAR(500) NOT NULL DEFAULT '',
  priority         VARCHAR(4)   NOT NULL DEFAULT '0.8',
  changefreq       VARCHAR(20)  NOT NULL DEFAULT 'weekly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
