-- 既存テーブル向け: ランク種別を追加
-- 手動実行用（マイグレーション不要）

ALTER TABLE `mst_disney_park_attraction`
  ADD COLUMN `rank_type` TINYINT NOT NULL DEFAULT 4 COMMENT '1=S / 2=A / 3=B / 4=C'
  AFTER `area_type`;

-- 初期データのランクをモック相当で更新
UPDATE `mst_disney_park_attraction` SET `rank_type` = 1 WHERE `attraction_name` = '美女と野獣“魔法のものがたり”';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 1 WHERE `attraction_name` = 'ベイマックスのハッピーライド（スペシャルバージョン）';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 1 WHERE `attraction_name` = 'スプラッシュ・マウンテン';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 2 WHERE `attraction_name` = 'ビッグサンダー・マウンテン';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 2 WHERE `attraction_name` = 'ホーンテッドマンション';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 2 WHERE `attraction_name` = 'スター・ツアーズ：ザ・アドベンチャーズ・コンティニュー';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'ガジェットのゴーコースター';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'ピーターパン空の旅';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'ジャングルクルーズ：ワイルドライフ・エクスペディション';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'カリブの海賊';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'ロジャーラビットのカートゥーンスピン';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 3 WHERE `attraction_name` = 'アリスのティーパーティー';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 4 WHERE `attraction_name` = 'ウエスタンリバー鉄道';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 4 WHERE `attraction_name` = '魅惑のチキルーム：スティッチ・プレゼンツ';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 4 WHERE `attraction_name` = 'スティッチ・エンカウンター';
UPDATE `mst_disney_park_attraction` SET `rank_type` = 4 WHERE `attraction_name` = 'ミッキーのフィルハーマジック';
