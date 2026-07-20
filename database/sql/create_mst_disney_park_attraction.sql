-- ディズニーパーク アトラクションマスタ
-- 手動実行用（マイグレーション不要）

CREATE TABLE `mst_disney_park_attraction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `park_type` TINYINT NOT NULL COMMENT '1=ランド / 2=シー',
  `attraction_name` VARCHAR(200) NOT NULL,
  `area_type` TINYINT NOT NULL COMMENT '1=WB / 2=AL / 3=WL / 4=CC / 5=FL / 6=TT / 7=TL',
  `rank_type` TINYINT NOT NULL COMMENT '1=S / 2=A / 3=B / 4=C',
  `duration_minutes` INT NULL DEFAULT NULL COMMENT '所要時間（分）',
  `thumb_url` VARCHAR(500) NULL DEFAULT NULL,
  `publish_start_date` DATE NULL DEFAULT NULL,
  `publish_end_date` DATE NULL DEFAULT NULL,
  `dpa_flag` TINYINT NOT NULL DEFAULT 0 COMMENT '0/1 DPA対象',
  `priority_pass_flag` TINYINT NOT NULL DEFAULT 0 COMMENT '0/1 プライオリティパス対象',
  `pause_flag` TINYINT NOT NULL DEFAULT 0 COMMENT '0=運営 / 1=休止',
  `created_datetime` DATETIME NULL DEFAULT NULL,
  `updated_datetime` DATETIME NULL DEFAULT NULL,
  `created_program_name` VARCHAR(100) NULL DEFAULT NULL,
  `updated_program_name` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ディズニーパーク アトラクションマスタ';
