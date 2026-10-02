-- ============================================================
-- Sujan Top-Up - Public demo schema
-- Structure only. No production data is included anywhere.
-- Derived from the legacy application's real table structure.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_token_expiry` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Admin users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `phone_number` VARCHAR(15) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login_attempts` INT(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Catalog: games / pricing / images / gallery / videos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `games` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `game_type` VARCHAR(50) NOT NULL,
  `uc_number` VARCHAR(255) NOT NULL,
  `original_price` DECIMAL(10,2) DEFAULT NULL,
  `discounted_price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_games_type` (`game_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `game_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `game_type` VARCHAR(255) NOT NULL,
  `game_name` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `image_gallery` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `image_name` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `videos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `thumbnail_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Reviews
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `rating` INT(11) NOT NULL,
  `review` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_feedback_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Generic order log
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `selected_game` VARCHAR(255) NOT NULL,
  `user_name` VARCHAR(255) NOT NULL,
  `user_email` VARCHAR(255) DEFAULT NULL,
  `phone_number` VARCHAR(20) DEFAULT NULL,
  `order_type` ENUM('whatsapp','website') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Payment proof images
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payimg` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `preid` INT(11) NOT NULL,
  `user_id` VARCHAR(255) DEFAULT NULL,
  `game_name` VARCHAR(255) DEFAULT NULL,
  `game_type` VARCHAR(255) DEFAULT NULL,
  `transaction_photo` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Per-service order tables
-- Website orders carry a user_id + status + payment image;
-- WhatsApp orders are lighter (no logged-in account required).
-- ============================================================

-- PUBG
CREATE TABLE IF NOT EXISTS `pubg_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uc_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(233) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pubg_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uc_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `username_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PUBG Global
CREATE TABLE IF NOT EXISTS `pubglobal_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uc_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pubglobal_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uc_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `username_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Free Fire
CREATE TABLE IF NOT EXISTS `freefire_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `freefire_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Free Fire Indonesia
CREATE TABLE IF NOT EXISTS `indonesiafreefire_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `indonesiafreefire_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mobile Legends
CREATE TABLE IF NOT EXISTS `mobilelegends_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mobilelegends_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mobile Legends Indonesia
CREATE TABLE IF NOT EXISTS `indonesiamobilelegends_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `indonesiamobilelegends_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MLBB (weekly pass style)
CREATE TABLE IF NOT EXISTS `mlbb_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mlb_userid` VARCHAR(233) NOT NULL,
  `user_id` VARCHAR(224) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mlbb_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `diamond_amount` VARCHAR(255) NOT NULL,
  `user_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clash of Clans
CREATE TABLE IF NOT EXISTS `clash_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `gem_amount` VARCHAR(255) NOT NULL,
  `supercell_email` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(233) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `clash_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `gem_amount` VARCHAR(255) NOT NULL,
  `supercell_email` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- eFootball Android
CREATE TABLE IF NOT EXISTS `efootball_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `konami_email` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `efootball_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `konami_email` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- eFootball iOS
CREATE TABLE IF NOT EXISTS `efootballios_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `konami_email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `payment_details` TEXT DEFAULT NULL,
  `payment_option` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `efootballios_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `konami_email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `payment_details` TEXT DEFAULT NULL,
  `payment_option` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TikTok
CREATE TABLE IF NOT EXISTS `tiktok_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `email_or_whatsapp` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tiktokuser_id` VARCHAR(233) NOT NULL,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tiktok_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `coin_amount` VARCHAR(255) NOT NULL,
  `email_or_whatsapp` VARCHAR(255) NOT NULL,
  `user_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Netflix
CREATE TABLE IF NOT EXISTS `netflix_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `netflix_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `player_id` VARCHAR(255) NOT NULL,
  `username_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Unpin
CREATE TABLE IF NOT EXISTS `unpin_website_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `player_id` VARCHAR(255) NOT NULL,
  `payment_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` VARCHAR(233) NOT NULL,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `unpin_whatsapp_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `player_id` VARCHAR(255) NOT NULL,
  `username_details` TEXT NOT NULL,
  `payment_option` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(233) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
