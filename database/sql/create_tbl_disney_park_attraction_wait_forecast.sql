-- ディズニーパーク アトラクション 時間帯別 予想待ち時間
-- 手動実行用（マイグレーション不要）
-- 1行 = アトラクション × 営業日 × 時間帯

CREATE TABLE `tbl_disney_park_attraction_wait_forecast` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `disney_park_attraction_id` INT UNSIGNED NOT NULL COMMENT 'mst_disney_park_attraction.id',
  `target_date` DATE NOT NULL COMMENT '対象営業日',
  `slot_time` TIME NOT NULL COMMENT '時間帯（例: 09:00:00）',
  `wait_minutes` INT NULL DEFAULT NULL COMMENT '予想待ち時間（分）。NULL=運営時間外',
  `created_datetime` DATETIME NULL DEFAULT NULL,
  `updated_datetime` DATETIME NULL DEFAULT NULL,
  `created_program_name` VARCHAR(100) NULL DEFAULT NULL,
  `updated_program_name` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ディズニーパーク アトラクション時間帯別予想待ち時間';
