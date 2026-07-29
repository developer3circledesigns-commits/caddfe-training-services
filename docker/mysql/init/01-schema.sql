-- ============================================================
-- CADDFE Training Services - Complete Database Schema
-- Database: u814177917_caddfe
-- ============================================================

CREATE DATABASE IF NOT EXISTS `u814177917_caddfe` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `u814177917_caddfe`;

-- ============================================================
-- 1. SITE SETTINGS
-- ============================================================
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_name` VARCHAR(255) NOT NULL DEFAULT 'CADDFE Training Services',
  `tagline` VARCHAR(500) DEFAULT NULL,
  `logo_light` VARCHAR(500) DEFAULT 'images/logo.png',
  `logo_dark` VARCHAR(500) DEFAULT NULL,
  `favicon` VARCHAR(500) DEFAULT 'images/fav_icon.png',
  `address` TEXT DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT '+91 99524 03574',
  `email` VARCHAR(255) DEFAULT 'magendhiran@caddfe.com',
  `whatsapp_number` VARCHAR(50) DEFAULT '+919500818276',
  `working_hours` VARCHAR(255) DEFAULT 'Mon – Sat: 9 AM – 8 PM',
  `footer_description` TEXT DEFAULT NULL,
  `copyright_text` VARCHAR(255) DEFAULT '© 2026 CADDFE. All rights reserved.',
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`site_name`, `tagline`, `address`, `phone`, `email`, `whatsapp_number`, `working_hours`, `footer_description`, `meta_description`) VALUES
('CADDFE Training Services', 'Shaping Ideas Into Practical Engineering Solutions', 'No:23, Thiruvasagam Street, Avadi, Chennai - 600072', '+91 99524 03574', 'magendhiran@caddfe.com', '+919500818276', 'Mon – Sat: 9 AM – 8 PM', 'CADDFE Training Services bridges the gap between academic learning and industry demands through hands-on Civil CAD training and professional architectural design services.', 'CADDFE Training Services — Professional Civil CAD training and architectural design services. Hands-on courses, industry-certified instructors, and career-ready programs.');

-- ============================================================
-- 2. SOCIAL MEDIA LINKS
-- ============================================================
CREATE TABLE `social_links` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `platform` VARCHAR(50) NOT NULL,
  `icon_class` VARCHAR(100) NOT NULL,
  `url` VARCHAR(500) NOT NULL DEFAULT '#',
  `sort_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `social_links` (`platform`, `icon_class`, `url`, `sort_order`) VALUES
('LinkedIn', 'bi bi-linkedin', '#', 1),
('YouTube', 'bi bi-youtube', '#', 2),
('Instagram', 'bi bi-instagram', '#', 3),
('Facebook', 'bi bi-facebook', '#', 4);

-- ============================================================
-- 3. COURSE CATEGORIES
-- ============================================================
CREATE TABLE `course_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `course_categories` (`slug`, `name`, `description`, `display_order`) VALUES
('all', 'All', 'All courses', 0),
('architecture', 'Architecture', 'Architecture diploma programs', 1),
('interior', 'Interior', 'Interior design diploma programs', 2),
('bim', 'BIM', 'BIM certification programs', 3),
('civil', 'Civil', 'Civil engineering courses', 4),
('diploma', 'Diploma Programs', 'Architectural & Interior Design diplomas with hands-on training', 5);

-- ============================================================
-- 4. COURSES
-- ============================================================
CREATE TABLE `courses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(500) NOT NULL,
  `slug` VARCHAR(500) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) NOT NULL,
  `hours` INT UNSIGNED DEFAULT NULL,
  `duration` VARCHAR(100) DEFAULT NULL,
  `levels` INT UNSIGNED DEFAULT NULL,
  `modules` INT UNSIGNED DEFAULT NULL,
  `assessments` INT UNSIGNED DEFAULT NULL,
  `tag` VARCHAR(100) DEFAULT NULL,
  `tag_color` VARCHAR(50) DEFAULT 'danger',
  `image` VARCHAR(500) DEFAULT NULL,
  `levels_detail` TEXT DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `display_order` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `course_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`category_id`, `name`, `slug`, `category`, `hours`, `duration`, `levels`, `modules`, `assessments`, `tag`, `tag_color`, `image`, `levels_detail`, `display_order`) VALUES
(1, 'Master Diploma in Architectural Design', 'master-diploma-architectural-design', 'architecture', 370, NULL, 3, NULL, NULL, 'Master', 'danger', 'images/courses_first/cad-course-clean-01.jpg', 'Level 1: 2D Architectural Presentation<br>Level 2: Advanced 3D Modelling<br>Level 3: Advanced Architectural Visualisation', 1),
(1, 'Advanced Diploma in Architectural Design', 'advanced-diploma-architectural-design', 'architecture', 200, NULL, 2, NULL, NULL, 'Advanced', 'warning text-dark', 'images/courses_first/cad-course-clean-02.jpg', 'Level 1: Advanced 3D Modelling<br>Level 2: Advanced Architectural Visualisation', 2),
(1, 'Diploma in Architectural Design', 'diploma-architectural-design', 'architecture', 100, NULL, 3, NULL, NULL, 'Diploma', 'info text-dark', 'images/courses_first/cad-course-clean-03.jpg', 'Level 1: Basic 2D Drafting<br>Level 2: Basic 3D Modelling<br>Level 3: Basic Architectural Visualisation', 3),
(2, 'Master Diploma in Interior Design', 'master-diploma-interior-design', 'interior', 250, NULL, 3, NULL, NULL, 'Master', 'danger', 'images/courses_first/cad-course-clean-04.jpg', 'Level 1: 2D Space Planning<br>Level 2: 3D Modelling<br>Level 3: Architectural Visualisation', 4),
(2, 'Advanced Diploma in Interior Design', 'advanced-diploma-interior-design', 'interior', 160, NULL, 3, NULL, NULL, 'Advanced', 'warning text-dark', 'images/courses_first/cad-course-clean-05.jpg', 'Level 1: Basic 2D Drafting<br>Level 2: Advanced 3D Modelling (Interior)<br>Level 3: Advanced Architectural Visualisation (Interior)', 5),
(2, 'Diploma in Interior Design', 'diploma-interior-design', 'interior', 70, NULL, 2, NULL, NULL, 'Diploma', 'info text-dark', 'images/courses_first/cad-course-clean-06.jpg', 'Level 1: Basic 3D Modelling (Interior)<br>Level 2: Basic Architectural Visualisation (Interior)', 6),
(3, 'BIM-Ready+ International Post Graduation Certification in BIM Management', 'bim-ready-plus-post-graduation', 'bim', 200, '10 Months', NULL, 10, 8, 'Post Graduate', 'dark', 'images/courses_first/cad-course-clean-07.jpg', NULL, 7),
(3, 'BIM-Ready Architecture Advanced', 'bim-ready-architecture-advanced', 'bim', 160, '8 Months', NULL, 14, 5, 'Architecture', 'primary', 'images/courses_first/cad-course-clean-08.jpg', NULL, 8),
(4, 'BIM-Ready Civil Course', 'bim-ready-civil', 'civil', 120, '6 Months', NULL, 6, 3, 'Civil', 'success', 'images/courses_first/cad-course-clean-09.jpg', NULL, 9),
(3, 'Michigan State University Certification Program in BIM', 'michigan-state-university-bim', 'bim', 100, '5 Months', NULL, 11, 3, 'University Program', 'primary', 'images/courses_first/cad-course-clean-10.jpg', NULL, 10),
(3, 'BIM-Ready Complete – International Certification in BIM Modeling & Coordination', 'bim-ready-complete', 'bim', 120, '6 Months', NULL, 9, 3, 'Professional', 'success', 'images/courses_first/cad-course-clean-11.jpg', NULL, 11),
(3, 'Building - SMART BIM Professional Certification', 'building-smart-bim', 'bim', 10, '10 Days', NULL, 6, 6, 'Professional', 'success', 'images/courses_first/cad-course-clean-12.jpg', NULL, 12);

-- ============================================================
-- 5. COURSE ENROLLMENTS
-- ============================================================
CREATE TABLE `enrollments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `dob` DATE DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `education` VARCHAR(500) DEFAULT NULL,
  `course_id` INT UNSIGNED DEFAULT NULL,
  `course_name` VARCHAR(500) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `photo_path` VARCHAR(500) DEFAULT NULL,
  `photo_data` MEDIUMBLOB DEFAULT NULL,
  `photo_mime` VARCHAR(50) DEFAULT NULL,
  `status` ENUM('pending', 'contacted', 'enrolled', 'cancelled') DEFAULT 'pending',
  `enquiry_source` VARCHAR(100) DEFAULT 'web',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL,
  INDEX `idx_enrollments_email` (`email`),
  INDEX `idx_enrollments_phone` (`phone`),
  INDEX `idx_enrollments_course` (`course_id`),
  INDEX `idx_enrollments_status` (`status`),
  INDEX `idx_enrollments_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //
CREATE TRIGGER `validate_enrollment_before_insert` BEFORE INSERT ON `enrollments`
FOR EACH ROW
BEGIN
  IF NEW.full_name IS NULL OR LENGTH(TRIM(NEW.full_name)) < 2 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Full name must be at least 2 characters';
  END IF;
  IF NEW.email IS NULL OR LOCATE('@', NEW.email) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A valid email address is required';
  END IF;
  IF NEW.phone IS NULL OR LENGTH(TRIM(NEW.phone)) < 7 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A valid phone number is required';
  END IF;
  IF NEW.dob IS NOT NULL AND NEW.dob > CURDATE() THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Date of birth cannot be in the future';
  END IF;
  IF NEW.course_id IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Please select a preferred course';
  END IF;
END//
DELIMITER ;

-- ============================================================
-- 6. CONTACT FORM SUBMISSIONS
-- ============================================================
CREATE TABLE `contact_submissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `subject` VARCHAR(500) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `is_replied` TINYINT(1) DEFAULT 0,
  `replied_at` TIMESTAMP NULL DEFAULT NULL,
  `reply_message` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. SERVICES
-- ============================================================
CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `short_description` TEXT DEFAULT NULL,
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `software_tags` TEXT DEFAULT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`title`, `slug`, `description`, `short_description`, `image`, `display_order`) VALUES
('Interior Design', 'interior-design', 'Space planning, material selection, lighting design, and furniture layout for residential and commercial interiors. Stunning 3D walkthroughs included. Our team specializes in creating functional yet aesthetically pleasing interiors that maximize space utilization while reflecting your personal style. From concept development to final execution, we handle every detail including color schemes, texture coordination, custom joinery, and ambient lighting solutions.', 'Complete interior design services with 3D walkthroughs', 'images/interior.jpg', 1),
('Structural Design', 'structural-design', 'RCC and steel structural design with seismic analysis. Safe, durable, and code-compliant structural solutions for all building types. We provide comprehensive structural engineering services including load analysis, foundation design, beam and column detailing, and slab reinforcement layouts. Our designs adhere to IS, ACI, and Eurocode standards, ensuring safety and longevity for residential, commercial, and industrial structures.', 'RCC & steel structural design with seismic analysis', 'images/structure.jpg', 2),
('Layout & Plan', 'layout-plan', 'Architectural floor plans, site layouts, and zoning diagrams. Optimized space utilization with seamless circulation and functionality. We create detailed 2D and 3D layout plans that cover everything from room dimensions and door/window placements to furniture layouts and traffic flow analysis. Our designs ensure optimal natural lighting, ventilation, and spatial efficiency while complying with local building codes and regulations.', 'Floor plans, site layouts, and zoning diagrams', 'images/planlayout.jpg', 3),
('3D Modelling', '3d-modelling', 'High-detail 3D models with realistic textures, lighting, and environments. Interactive walkthroughs and VR-ready visualizations. We use industry-leading software including 3ds Max, SketchUp, and Lumion to produce photorealistic renders with accurate material properties, daylight simulation, and camera-matched perspectives. Our 3D services also include animated walkthrough videos and virtual tour experiences for client presentations and marketing.', 'Photorealistic 3D models with VR-ready visualizations', 'images/3dmodel.jpg', 4),
('Elevation Design', 'elevation-design', 'Front, rear, and side elevation designs with aesthetic façade treatments. Modern, contemporary, and traditional style options available. Our elevation designs focus on curb appeal and architectural harmony, incorporating elements such as cladding materials, window patterns, roof styles, balcony details, and exterior color palettes. We provide photorealistic elevation views with shadow analysis and material callouts.', 'Façade design with modern, contemporary & traditional styles', 'images/elevation.jpg', 5);

-- ============================================================
-- 8. PROJECTS PORTFOLIO
-- ============================================================
CREATE TABLE `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `additional_images` JSON DEFAULT NULL,
  `client_name` VARCHAR(255) DEFAULT NULL,
  `project_date` DATE DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `software_used` TEXT DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('completed', 'ongoing', 'planned') DEFAULT 'completed',
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. TESTIMONIALS
-- ============================================================
CREATE TABLE `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `client_name` VARCHAR(255) NOT NULL,
  `client_title` VARCHAR(255) DEFAULT NULL,
  `avatar` VARCHAR(500) DEFAULT NULL,
  `rating` TINYINT UNSIGNED DEFAULT 5,
  `content` TEXT NOT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. STATIC COUNTERS (from homepage stats)
-- ============================================================
CREATE TABLE `site_counters` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(255) NOT NULL,
  `value` INT UNSIGNED NOT NULL DEFAULT 0,
  `suffix` VARCHAR(50) DEFAULT '+',
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_counters` (`label`, `value`, `suffix`, `display_order`) VALUES
('Students Trained', 2500, '+', 1),
('Courses Offered', 12, '+', 2),
('Expert Mentors', 15, '+', 3),
('Projects Delivered', 500, '+', 4);

-- ============================================================
-- 11. NEWSLETTER SUBSCRIBERS
-- ============================================================
CREATE TABLE `subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `is_active` TINYINT(1) DEFAULT 1,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. NAVIGATION MENU ITEMS
-- ============================================================
CREATE TABLE `navigation_menus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(255) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `menu_location` ENUM('header', 'footer', 'both') DEFAULT 'both',
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_mega_menu` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `navigation_menus`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `navigation_menus` (`label`, `url`, `menu_location`, `display_order`) VALUES
('Home', 'index.php', 'header', 1),
('Courses', '#', 'header', 2),
('Services', 'services.php', 'header', 3),
('Projects', 'projects.php', 'header', 4),
('Contact Us', 'contact_us.php', 'header', 5),
('Home', 'index.php', 'footer', 1),
('Programs', 'courses.php', 'footer', 2),
('About Us', '#', 'footer', 3),
('Contact', 'contact_us.php', 'footer', 4),
('Enroll Now', '#', 'footer', 5);

INSERT INTO `navigation_menus` (`label`, `url`, `parent_id`, `menu_location`, `display_order`, `is_mega_menu`) VALUES
('Diploma Programs', 'courses.php?cat=diploma', 2, 'header', 1, 1),
('BIM Programs', 'courses.php?cat=bim', 2, 'header', 2, 1);

-- ============================================================
-- 13. HERO SECTION SLIDES
-- ============================================================
CREATE TABLE `hero_slides` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `subtitle` TEXT DEFAULT NULL,
  `image_desktop` VARCHAR(500) DEFAULT NULL,
  `image_mobile` VARCHAR(500) DEFAULT NULL,
  `button_text` VARCHAR(100) DEFAULT NULL,
  `button_url` VARCHAR(500) DEFAULT NULL,
  `display_order` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hero_slides` (`title`, `subtitle`, `image_desktop`, `image_mobile`, `button_text`, `button_url`, `display_order`) VALUES
('Shaping Ideas Into Practical Engineering Solutions', 'Professional Engineering Solutions', 'images/hero3-1920.jpg', 'images/hero3-960.jpg', 'Explore programs →', 'courses.php', 1);

-- ============================================================
-- 14. ADMIN USERS
-- ============================================================
CREATE TABLE `admin_users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `role` ENUM('superadmin', 'admin', 'editor') DEFAULT 'admin',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin / admin123 (change immediately in production)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES
('admin', 'admin@caddfe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'superadmin');

-- ============================================================
-- 15. WHATSAPP ENQUIRY LOGS
-- ============================================================
CREATE TABLE `whatsapp_enquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT UNSIGNED DEFAULT NULL,
  `course_name` VARCHAR(500) NOT NULL,
  `phone_number` VARCHAR(50) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. ACTIVITY LOGS
-- ============================================================
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDEXES
-- ============================================================
CREATE INDEX `idx_courses_category` ON `courses`(`category`);
CREATE INDEX `idx_courses_active` ON `courses`(`is_active`);
CREATE INDEX `idx_courses_featured` ON `courses`(`is_featured`);
CREATE INDEX `idx_contact_read` ON `contact_submissions`(`is_read`);
CREATE INDEX `idx_enrollments_status` ON `enrollments`(`status`);
CREATE INDEX `idx_projects_category` ON `projects`(`category`);
CREATE INDEX `idx_projects_featured` ON `projects`(`is_featured`);
CREATE INDEX `idx_testimonials_active` ON `testimonials`(`is_active`);
CREATE INDEX `idx_subscribers_active` ON `subscribers`(`is_active`);
CREATE INDEX `idx_navigation_location` ON `navigation_menus`(`menu_location`);
CREATE INDEX `idx_activity_user` ON `activity_logs`(`user_id`);
CREATE INDEX `idx_activity_created` ON `activity_logs`(`created_at`);

-- ============================================================
-- VIEW: Active Courses with Category Names
-- ============================================================
CREATE VIEW `active_courses_view` AS
SELECT
  c.*,
  cat.name AS category_name,
  cat.slug AS category_slug
FROM `courses` c
LEFT JOIN `course_categories` cat ON c.category_id = cat.id
WHERE c.is_active = 1
ORDER BY c.display_order ASC;

-- ============================================================
-- VIEW: Unread Contact Submissions
-- ============================================================
CREATE VIEW `unread_contacts_view` AS
SELECT * FROM `contact_submissions` WHERE `is_read` = 0 ORDER BY `created_at` DESC;

-- ============================================================
-- VIEW: Pending Enrollments
-- ============================================================
CREATE VIEW `pending_enrollments_view` AS
SELECT * FROM `enrollments` WHERE `status` = 'pending' ORDER BY `created_at` DESC;
