-- ディズニーパーク アトラクションマスタ 初期データ（TDL）
-- duration_minutes: 「○分30秒」は四捨五入（1分30秒→2, 2分30秒→3, 3分30秒→4, 4分30秒→5）
-- rank_type: 1=S / 2=A / 3=B / 4=C

INSERT INTO `mst_disney_park_attraction` (
  `park_type`,
  `attraction_name`,
  `area_type`,
  `rank_type`,
  `duration_minutes`,
  `thumb_url`,
  `publish_start_date`,
  `publish_end_date`,
  `dpa_flag`,
  `priority_pass_flag`,
  `pause_flag`,
  `created_datetime`,
  `updated_datetime`,
  `created_program_name`,
  `updated_program_name`
) VALUES
(1, '美女と野獣“魔法のものがたり”', 5, 1, 8, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1054_thum_name.jpg', NULL, NULL, 1, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ベイマックスのハッピーライド（スペシャルバージョン）', 7, 1, 2, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1047_thum_name.jpg', NULL, NULL, 1, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'スプラッシュ・マウンテン', 4, 1, 10, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/351_thum_name.jpg', NULL, NULL, 1, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ビッグサンダー・マウンテン', 3, 2, 4, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/461_thum_name.jpg', NULL, NULL, 0, 1, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ホーンテッドマンション', 5, 2, 15, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/611_thum_name.jpg', NULL, NULL, 0, 1, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'スター・ツアーズ：ザ・アドベンチャーズ・コンティニュー', 7, 2, 5, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/311_thum_name.jpg', NULL, NULL, 0, 1, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ガジェットのゴーコースター', 6, 3, 1, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/81_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ピーターパン空の旅', 5, 3, 3, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/521_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ジャングルクルーズ：ワイルドライフ・エクスペディション', 2, 3, 10, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/231_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'カリブの海賊', 2, 3, 15, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/51_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ロジャーラビットのカートゥーンスピン', 6, 3, 4, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/381_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'アリスのティーパーティー', 5, 3, 2, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ウエスタンリバー鉄道', 2, 4, 15, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/31_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, '魅惑のチキルーム：スティッチ・プレゼンツ', 2, 4, 10, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/571_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'スティッチ・エンカウンター', 7, 4, 12, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/681_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert'),
(1, 'ミッキーのフィルハーマジック', 5, 4, 16, 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/641_thum_name.jpg', NULL, NULL, 0, 0, 0, NOW(), NOW(), 'manual_insert', 'manual_insert');
