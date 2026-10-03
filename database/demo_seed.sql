-- ============================================================
-- Sujan Top-Up - Fictional demo seed data
--
-- Everything in this file is invented. No real customer, order,
-- payment or personal data is included. Passwords are stored only
-- as password_hash() values.
-- ============================================================

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Demo accounts
--   user : demo.user@example.test  / DemoUser@123
--   admin: demo_admin              / DemoAdmin@123
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `address`) VALUES
  (1, 'Demo User', '9800000001', 'demo.user@example.test',
   '$2y$10$30p3K2TFtF3sZvIPsrs.ZOJ6PHPojbRVQMRjaOMhloq41sINn5Asi',
   'Demo Street 1, Kathmandu'),
  (2, 'Sample Player', '9800000002', 'sample.player@example.test',
   '$2y$10$30p3K2TFtF3sZvIPsrs.ZOJ6PHPojbRVQMRjaOMhloq41sINn5Asi',
   'Demo Street 2, Pokhara');

INSERT INTO `admin_users` (`id`, `username`, `email`, `phone_number`, `password`, `login_attempts`) VALUES
  (1, 'demo_admin', 'demo.admin@example.test', '9800000000',
   '$2y$10$GYKIyFglXR69xFml4OoMx.iAT0A6IO0GKhovhjHqJZK44wb4acy.y', 0);

-- ------------------------------------------------------------
-- Fictional catalog pricing (per service)
-- ------------------------------------------------------------
INSERT INTO `games` (`game_type`, `uc_number`, `original_price`, `discounted_price`) VALUES
  ('pubg', '60 UC', 220.00, 200.00),
  ('pubg', '325 UC', NULL, 825.00),
  ('pubg', '660 UC', 1500.00, 1400.00),
  ('pubg-global', '60 UC', 230.00, 210.00),
  ('pubg-global', '325 UC', NULL, 850.00),
  ('freefire', '25 Diamonds', 40.00, 30.00),
  ('freefire', '115 Diamonds', NULL, 100.00),
  ('freefire', '480 Diamonds', 500.00, 450.00),
  ('free-fire-indonesia', '50 Diamonds', 60.00, 50.00),
  ('free-fire-indonesia', '355 Diamonds', NULL, 340.00),
  ('mobilelegends', '86 Diamonds', 240.00, 220.00),
  ('mobilelegends', '172 Diamonds', 470.00, 450.00),
  ('Mobile Legend Indonesia', '50 Diamonds', 55.00, 50.00),
  ('mlbb', 'Weekly Pass', 1400.00, 999.00),
  ('clash of clan', '500 Gems', 700.00, 650.00),
  ('clash of clan', '1200 Gems', 1600.00, 1500.00),
  ('Efootball Android', '500 Coins', 600.00, 550.00),
  ('Efootball Ios', '500 Coins', 620.00, 570.00),
  ('tiktok', '100 Coins', 200.00, 180.00),
  ('tiktok', '500 Coins', 950.00, 900.00),
  ('netflix', '1 Month', 1200.00, 1100.00),
  ('unpin', '1 Month', 500.00, 450.00);

-- ------------------------------------------------------------
-- Fictional game images (paths point at the public assets dir)
-- ------------------------------------------------------------
-- IDs are kept aligned with the legacy home page, which looks images up
-- by a stable id. Paths point at first-party demo placeholders.
INSERT INTO `game_images` (`id`, `game_type`, `game_name`, `image_path`) VALUES
  (2, 'clash', 'Clash of Clans', 'https://upload.wikimedia.org/wikipedia/en/5/59/Clash_of_Clans_Logo.png'),
  (3, 'efootball', 'eFootball Android', 'https://upload.wikimedia.org/wikipedia/commons/e/ee/EFootball_logo.svg'),
  (4, 'freefire', 'Free Fire', 'https://upload.wikimedia.org/wikipedia/en/c/c5/Logo_of_Garena_Free_Fire.png'),
  (5, 'freefire_indonesia', 'Free Fire Indonesia', 'https://upload.wikimedia.org/wikipedia/en/c/c5/Logo_of_Garena_Free_Fire.png'),
  (6, 'efootball_ios', 'eFootball iOS', 'https://upload.wikimedia.org/wikipedia/commons/e/ee/EFootball_logo.svg'),
  (7, 'team', 'Demo Team', 'assets/img/avatar-placeholder.svg'),
  (8, 'mlbb', 'Mobile Legends: Bang Bang', 'https://upload.wikimedia.org/wikipedia/en/a/a0/Mobile_Legends_Bang_Bang_2025_logo.png'),
  (9, 'mobilelegend_indonesia', 'Mobile Legends Indonesia', 'https://upload.wikimedia.org/wikipedia/en/a/a0/Mobile_Legends_Bang_Bang_2025_logo.png'),
  (10, 'mobilelegend', 'Mobile Legends', 'https://upload.wikimedia.org/wikipedia/en/a/a0/Mobile_Legends_Bang_Bang_2025_logo.png'),
  (11, 'pubg', 'PUBG Mobile', 'https://upload.wikimedia.org/wikipedia/en/4/44/PlayerUnknown%27s_Battlegrounds_Mobile.webp'),
  (12, 'pubg_global', 'PUBG Mobile Global', 'https://upload.wikimedia.org/wikipedia/en/4/44/PlayerUnknown%27s_Battlegrounds_Mobile.webp'),
  (13, 'tiktok', 'TikTok', 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Tiktok_logo.png'),
  (14, 'team', 'Demo Team', 'assets/img/avatar-placeholder.svg'),
  (15, 'team', 'Demo Team', 'assets/img/avatar-placeholder.svg'),
  (16, 'team', 'Demo Team', 'assets/img/avatar-placeholder.svg'),
  (17, 'team', 'Demo Team', 'assets/img/avatar-placeholder.svg'),
  (20, 'spotify', 'Spotify', 'https://upload.wikimedia.org/wikipedia/commons/9/99/Black_Spotify_logo_with_text.svg'),
  (21, 'netflix', 'Netflix', 'https://upload.wikimedia.org/wikipedia/commons/6/69/Netflix_logo.svg'),
  (22, 'prime', 'Prime Video', 'https://upload.wikimedia.org/wikipedia/commons/9/90/Prime_Video_logo_%282024%29.svg'),
  (23, 'unpin', 'Unpin', 'assets/img/games/generic.svg');

-- ------------------------------------------------------------
-- Fictional reviews
-- ------------------------------------------------------------
INSERT INTO `feedback` (`username`, `type`, `rating`, `review`) VALUES
  ('Demo User', 'Overall Website and Service', 5, 'Smooth demo checkout and clear order tracking.'),
  ('Sample Player', 'PUBG', 4, 'Order status updated quickly in the demo.'),
  ('Demo User', 'Free Fire', 5, 'Nice that the demo shows the full order flow.'),
  ('Sample Player', 'Mobile legend', 3, 'Works well for a preserved legacy project.');

-- ------------------------------------------------------------
-- Fictional orders (website + whatsapp)
-- ------------------------------------------------------------
INSERT INTO `pubg_website_orders` (`uc_amount`, `player_id`, `payment_details`, `payment_option`, `user_id`, `status`) VALUES
  ('60 UC', '5123456789', 'demo_player', 'esewa', '1', 'pending'),
  ('325 UC', '5987654321', 'sample_player', 'khalti', '2', 'confirmed');

INSERT INTO `freefire_website_orders` (`diamond_amount`, `player_id`, `payment_details`, `payment_option`, `user_id`, `status`) VALUES
  ('25 Diamonds', '900123456', 'demo_player', 'esewa', '1', 'completed');

INSERT INTO `mobilelegends_website_orders` (`diamond_amount`, `player_id`, `payment_details`, `payment_option`, `user_id`, `status`) VALUES
  ('86 Diamonds', '123456789', 'sample_player', 'imepay', '2', 'pending');

INSERT INTO `tiktok_website_orders` (`coin_amount`, `email_or_whatsapp`, `payment_details`, `payment_option`, `tiktokuser_id`, `user_id`, `status`) VALUES
  ('100 Coins', 'demo.user@example.test', 'demo_player', 'esewa', 'demo_tiktok', '1', 'pending');

INSERT INTO `freefire_whatsapp_orders` (`diamond_amount`, `player_id`, `payment_details`, `payment_option`, `status`) VALUES
  ('50 Diamonds', '911222333', 'whatsapp guest', 'esewa', 'pending');

INSERT INTO `orders` (`selected_game`, `user_name`, `user_email`, `phone_number`, `order_type`) VALUES
  ('Free Fire', 'Demo User', 'demo.user@example.test', '9800000001', 'website'),
  ('PUBG', 'Sample Player', 'sample.player@example.test', '9800000002', 'whatsapp');
