<?php

namespace App\Apis\Showtimes\Mock;

/**
 * TDL ショー&パレード / 混雑モックデータ
 * 本実装時はDB・外部API取得に置換する
 */
class TdlShowtimesMock
{
    /**
     * @return array<string, mixed>
     */
    public static function forDate(string $date): array
    {
        // モック段階では日付に関わらず同一データを返す
        return [
            'park' => [
                'id' => 'tdl',
                'name' => 'Tokyo Disneyland',
                'nameJa' => '東京ディズニーランド',
                'date' => $date,
                'dayOfWeek' => '火',
                'openTime' => '9:00',
                'closeTime' => '21:00',
                'seasonTag' => '夏イベント「ベイマックスのミッション・クールダウン」開催期間（7/2〜9/14）',
                'officialShowUrl' => 'https://www.tokyodisneyresort.jp/tdl/show.html',
                'crowdSourceUrl' => 'https://urtrip.jp/tdl-attraction-waitingtime-data/#recommended_time',
            ],
            'shows' => self::shows(),
            'timeline' => self::timeline(),
            'excludedPrograms' => [
                [
                    'name' => 'ミッキーのレインボー・ルアウ',
                    'note' => 'ポリネシアンテラス・レストラン — 事前予約制。公演時刻は予約時に確認。',
                ],
            ],
            'stoppedPrograms' => [
                [
                    'name' => 'スカイ・フル・オブ・カラーズ（花火）',
                    'note' => '6/15〜9/14 休止中のため公演なし',
                ],
                [
                    'name' => 'ザ・ダイヤモンド・バラエティマスター',
                    'note' => '4/1〜11/20 休止中',
                ],
                [
                    'name' => 'イッツ・ア・スウィーツフルタイム！',
                    'note' => '6/30で公演終了',
                ],
            ],
            'crowd' => self::crowd(),
            'footer' => [
                'sources' => [
                    '東京ディズニーリゾート公式サイト（パレード/ショー一覧・各ショーの月間スケジュール、2026年7月時点）',
                    'アトラクション混雑の目安は一般的な傾向をまとめたものです。詳細はurtripの過去データ分析をご参照ください。',
                    '公演時間は予告なく変更・中止になる場合があります。当日は東京ディズニーリゾート・アプリで最新情報をご確認ください。',
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function shows(): array
    {
        return [
            [
                'id' => 'baymax',
                'name' => 'ベイマックスのミッション・クールダウン',
                'colorKey' => 'baymax',
                'color' => '#5FD8D2',
                'location' => 'パークワイド',
                'duration' => '約35分',
                'frequency' => '1日3回',
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/2467_thum_name.jpg',
                'badges' => [
                    ['type' => 'wet', 'label' => 'びしょ濡れ注意'],
                ],
            ],
            [
                'id' => 'mmw',
                'name' => 'ミッキーのマジカルミュージックワールド',
                'colorKey' => 'mmw',
                'color' => '#7FB4F2',
                'location' => 'ファンタジーランド',
                'duration' => '約25分',
                'frequency' => '屋内',
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/1920_thum_name.jpg',
                'badges' => [
                    ['type' => 'entry', 'label' => 'エントリー受付'],
                    ['type' => 'dpa', 'label' => 'DPA対象'],
                    ['type' => 'info', 'label' => '7/1〜9/14は2階席自由席'],
                ],
            ],
            [
                'id' => 'harmony',
                'name' => 'ディズニー・ハーモニー・イン・カラー',
                'colorKey' => 'harmony',
                'color' => '#FF7E6B',
                'location' => '昼パレード',
                'duration' => '約45分',
                'frequency' => null,
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/2368_thum_name.jpg',
                'badges' => [
                    ['type' => 'dpa', 'label' => 'DPA対象'],
                ],
            ],
            [
                'id' => 'jamboree',
                'name' => 'ジャンボリミッキー！レッツ・ダンス！',
                'colorKey' => 'jamboree',
                'color' => '#8ED07A',
                'location' => 'アドベンチャーランド',
                'duration' => '約15分',
                'frequency' => null,
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/2207_thum_name.jpg',
                'badges' => [
                    ['type' => 'entry', 'label' => 'エントリー受付'],
                ],
            ],
            [
                'id' => 'elec',
                'name' => 'エレクトリカルパレード・ドリームライツ',
                'colorKey' => 'elec',
                'color' => '#F5C86B',
                'location' => '夜パレード',
                'duration' => '約45分',
                'frequency' => null,
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/1_thum_name.jpg',
                'badges' => [
                    ['type' => 'dpa', 'label' => 'DPA対象'],
                ],
            ],
            [
                'id' => 'stars',
                'name' => 'Reach for the Stars: Everlasting Dreams',
                'colorKey' => 'stars',
                'color' => '#B79BF2',
                'location' => 'キャッスルショー',
                'duration' => '約25分（夏期版）',
                'frequency' => null,
                'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/show/2565_thum_name.jpg',
                'badges' => [
                    ['type' => 'dpa', 'label' => 'DPA対象'],
                    ['type' => 'info', 'label' => '9/14公演終了'],
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function timeline(): array
    {
        return [
            ['type' => 'phase', 'label' => '午前'],
            [
                'type' => 'slot',
                'showId' => 'baymax',
                'time' => '10:00',
                'title' => 'ベイマックスのミッション・クールダウン ①',
                'location' => 'パークワイド',
                'meta' => '約35分 ／ 散水演出あり・濡れます',
            ],
            [
                'type' => 'slot',
                'showId' => 'mmw',
                'time' => '10:50',
                'title' => 'ミッキーのマジカルミュージックワールド ①',
                'location' => 'ファンタジーランド',
                'meta' => '約25分 ／ エントリー・DPA',
            ],
            ['type' => 'phase', 'label' => '昼'],
            [
                'type' => 'slot',
                'showId' => 'mmw',
                'time' => '12:15',
                'title' => 'ミッキーのマジカルミュージックワールド ②',
                'location' => 'ファンタジーランド',
                'meta' => '約25分',
            ],
            [
                'type' => 'slot',
                'showId' => 'baymax',
                'time' => '12:50',
                'title' => 'ベイマックスのミッション・クールダウン ②',
                'location' => 'パークワイド',
                'meta' => '約35分',
            ],
            [
                'type' => 'slot',
                'showId' => 'mmw',
                'time' => '13:40',
                'title' => 'ミッキーのマジカルミュージックワールド ③',
                'location' => 'ファンタジーランド',
                'meta' => '約25分',
            ],
            [
                'type' => 'slot',
                'showId' => 'baymax',
                'time' => '14:55',
                'title' => 'ベイマックスのミッション・クールダウン ③（最終回）',
                'location' => 'パークワイド',
                'meta' => '約35分',
            ],
            [
                'type' => 'slot',
                'showId' => 'mmw',
                'time' => '15:45',
                'title' => 'ミッキーのマジカルミュージックワールド ④',
                'location' => 'ファンタジーランド',
                'meta' => '約25分',
            ],
            ['type' => 'phase', 'label' => '夕方'],
            [
                'type' => 'slot',
                'showId' => 'harmony',
                'time' => '17:00',
                'title' => 'ディズニー・ハーモニー・イン・カラー',
                'location' => '昼パレード（パレードルート）',
                'meta' => '約45分 ／ DPA対象',
            ],
            [
                'type' => 'slot',
                'showId' => 'mmw',
                'time' => '17:10',
                'title' => 'ミッキーのマジカルミュージックワールド ⑤（最終回）',
                'location' => 'ファンタジーランド',
                'meta' => '約25分 ／ ※パレードと時間帯が重なります',
            ],
            [
                'type' => 'slot',
                'showId' => 'jamboree',
                'time' => '18:00',
                'title' => 'ジャンボリミッキー！レッツ・ダンス！ ①',
                'location' => 'アドベンチャーランド',
                'meta' => '約15分 ／ エントリー受付',
            ],
            ['type' => 'phase', 'label' => '夜'],
            [
                'type' => 'slot',
                'showId' => 'jamboree',
                'time' => '19:20',
                'title' => 'ジャンボリミッキー！レッツ・ダンス！ ②',
                'location' => 'アドベンチャーランド',
                'meta' => '約15分',
            ],
            [
                'type' => 'slot',
                'showId' => 'elec',
                'time' => '19:45',
                'title' => 'エレクトリカルパレード・ドリームライツ',
                'location' => '夜パレード（パレードルート）',
                'meta' => '約45分 ／ DPA対象',
            ],
            [
                'type' => 'slot',
                'showId' => 'jamboree',
                'time' => '20:35',
                'title' => 'ジャンボリミッキー！レッツ・ダンス！ ③（最終回）',
                'location' => 'アドベンチャーランド',
                'meta' => '約15分',
            ],
            [
                'type' => 'slot',
                'showId' => 'stars',
                'time' => '20:55',
                'title' => 'Reach for the Stars: Everlasting Dreams',
                'location' => 'シンデレラ城前',
                'meta' => '約25分 ／ DPA対象 ／ 1日のフィナーレ',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function crowd(): array
    {
        return [
            'slots' => [
                '9:00', '10:00', '11:00', '12:00', '13:00', '14:00',
                '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '20:45',
            ],
            'showMarkers' => [
                '1' => 'ベイマックス①10:00',
                '4' => 'ベイマックス②12:50',
                '6' => 'ベイマックス③14:55',
                '8' => 'ハーモニー17:00',
                '11' => 'エレパレ19:45',
                '12' => 'Reach for the Stars 20:55',
            ],
            'summaryNote' => 'ランク別 平均待ち時間（7/21 夏期・平日の推定目安）',
            'notes' => [
                '待ち時間は夏期・平日の一般的な傾向に基づく推定目安です。当日の実測はurtripのヒートマップと東京ディズニーリゾート・アプリで確認してください。',
                '▼マークはショーパレの開催時刻（10:00 / 12:50 / 14:55 ベイマックス、17:00 ハーモニー、19:45 エレパレ、20:55 Reach for the Stars）。パレード中はルートから離れたアトラクションの待ちが下がる傾向があります。',
                'スペース・マウンテンは建て替えのため休止中です。',
            ],
            'attractions' => [
                [
                    'id' => 'beauty-beast',
                    'name' => '美女と野獣“魔法のものがたり”',
                    'area' => 'ファンタジーランド',
                    'rank' => 'S',
                    'wait' => [45, 95, 110, 120, 115, 110, 105, 100, 90, 85, 70, 55, 40],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-'],
                    'tip' => '開園ダッシュ最有力。DPAは午後早めに売切れがち。エレパレ中〜閉園前が第二の狙い目',
                ],
                [
                    'id' => 'baymax-ride',
                    'name' => 'ベイマックスのハッピーライド',
                    'area' => 'トゥモローランド',
                    'rank' => 'S',
                    'wait' => [35, 80, 100, 110, 105, 100, 95, 90, 80, 70, 60, 45, 35],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '夜は照明演出で別体験。19時以降に待ちが下がりやすい',
                ],
                [
                    'id' => 'splash',
                    'name' => 'スプラッシュ・マウンテン',
                    'area' => 'クリッターカントリー',
                    'rank' => 'S',
                    'wait' => [30, 70, 90, 105, 100, 95, 90, 85, 75, 65, 55, 45, 35],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '夏はびしょ濡れ需要でピーク長め。ベイマックスの散水ショーと使い分けを',
                ],
                [
                    'id' => 'big-thunder',
                    'name' => 'ビッグサンダー・マウンテン',
                    'area' => 'ウエスタンランド',
                    'rank' => 'A',
                    'wait' => [15, 40, 55, 60, 60, 55, 55, 50, 45, 40, 35, 25, 20],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-'],
                    'tip' => 'プライオリティパス対象。開園直後にPP取得→午前中に消化が効率的',
                ],
                [
                    'id' => 'pooh',
                    'name' => 'プーさんのハニーハント',
                    'area' => 'ファンタジーランド',
                    'rank' => 'A',
                    'wait' => [20, 45, 60, 65, 60, 60, 55, 50, 45, 40, 35, 30, 25],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-', '-'],
                    'tip' => '屋内で夏の暑さ対策にも◎。PPは朝のうちに',
                ],
                [
                    'id' => 'monsters',
                    'name' => 'モンスターズ・インク“ライド&ゴーシーク”',
                    'area' => 'トゥモローランド',
                    'rank' => 'A',
                    'wait' => [25, 50, 65, 70, 65, 65, 60, 55, 50, 45, 40, 30, 25],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-', '-'],
                    'tip' => '屋内・冷房あり。昼ピークの避難先としても優秀',
                ],
                [
                    'id' => 'haunted',
                    'name' => 'ホーンテッドマンション',
                    'area' => 'ファンタジーランド',
                    'rank' => 'A',
                    'wait' => [13, 30, 40, 45, 45, 40, 40, 35, 30, 30, 25, 20, 15],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '回転が速く見た目より進む。夕方以降が快適',
                ],
                [
                    'id' => 'peter-pan',
                    'name' => 'ピーターパン空の旅',
                    'area' => 'ファンタジーランド',
                    'rank' => 'B',
                    'wait' => [15, 30, 40, 45, 40, 40, 35, 35, 30, 25, 20, 15, 13],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '定番の割に列が伸びやすい。17:00のパレード中が狙い目',
                ],
                [
                    'id' => 'jungle-cruise',
                    'name' => 'ジャングルクルーズ',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'B',
                    'wait' => [10, 25, 35, 40, 40, 35, 35, 30, 25, 25, 20, 15, 10],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'ナイトクルーズは雰囲気抜群。日中の炎天下は避けたい',
                ],
                [
                    'id' => 'pirates',
                    'name' => 'カリブの海賊',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'B',
                    'wait' => [5, 15, 25, 30, 30, 25, 25, 20, 15, 15, 13, 10, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '涼しい屋内ボートライド。昼ピークの休憩に最適',
                ],
                [
                    'id' => 'small-world',
                    'name' => 'イッツ・ア・スモールワールド',
                    'area' => 'ファンタジーランド',
                    'rank' => 'B',
                    'wait' => [5, 15, 25, 30, 25, 25, 25, 20, 20, 15, 13, 10, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '冷房完備で回転が速い。子連れの昼の避難先',
                ],
                [
                    'id' => 'star-tours',
                    'name' => 'スター・ツアーズ',
                    'area' => 'トゥモローランド',
                    'rank' => 'C',
                    'wait' => [5, 13, 20, 25, 25, 20, 20, 15, 13, 13, 10, 5, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'ほぼ待たずに乗れる日が多い。時間調整に',
                ],
                [
                    'id' => 'western-river',
                    'name' => 'ウエスタンリバー鉄道',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'C',
                    'wait' => [5, 10, 15, 20, 20, 15, 15, 15, 13, 10, 10, 5, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '座って回れる休憩系。夕暮れ便がおすすめ',
                ],
                [
                    'id' => 'tiki-room',
                    'name' => '魅惑のチキルーム',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'C',
                    'wait' => [5, 5, 10, 10, 10, 10, 10, 10, 5, 5, 5, 5, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '冷房×着席。真夏の昼の最強リフレッシュ枠',
                ],
            ],
        ];
    }
}
