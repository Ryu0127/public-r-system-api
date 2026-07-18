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
            'food' => self::food(),
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
                '所要時間は東京ディズニーリゾート公式サイト掲載の体験時間です（待ち時間は含みません）。',
                '待ち時間は夏期・平日の一般的な傾向に基づく推定目安です。当日の実測はurtripのヒートマップと東京ディズニーリゾート・アプリで確認してください。',
                '▼マークはショーパレの開催時刻（10:00 / 12:50 / 14:55 ベイマックス、17:00 ハーモニー、19:45 エレパレ、20:55 Reach for the Stars）。パレード中はルートから離れたアトラクションの待ちが下がる傾向があります。',
                'スペース・マウンテンは建て替えのため休止中です。',
            ],
            'attractions' => [
                [
                    'id' => 'beauty-beast',
                    'name' => '美女と野獣“魔法のものがたり”',
                    'duration' => '約8分',
                    'area' => 'ファンタジーランド',
                    'rank' => 'S',
                    'pid' => 197,
                    'icon' => 'castle',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1054_thum_name.jpg',
                    'wait' => [45, 95, 110, 120, 115, 110, 105, 100, 90, 85, 70, 55, 40],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-'],
                    'tip' => '開園ダッシュ最有力。DPAは午後早めに売切れがち。エレパレ中〜閉園前が第二の狙い目',
                ],
                [
                    'id' => 'baymax-ride',
                    'name' => 'ベイマックスのハッピーライド（スペシャルバージョン）',
                    'duration' => '約1分30秒',
                    'area' => 'トゥモローランド',
                    'rank' => 'S',
                    'pid' => 196,
                    'icon' => 'robot',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1047_thum_name.jpg',
                    'wait' => [35, 80, 100, 110, 105, 100, 95, 90, 80, 70, 60, 45, 35],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '19時以降に待ちが下がりやすい',
                ],
                [
                    'id' => 'splash',
                    'name' => 'スプラッシュ・マウンテン',
                    'duration' => '約10分',
                    'area' => 'クリッターカントリー',
                    'rank' => 'S',
                    'pid' => 162,
                    'icon' => 'splash',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/351_thum_name.jpg',
                    'wait' => [30, 70, 90, 105, 100, 95, 90, 85, 75, 65, 55, 45, 35],
                    'pass' => ['A', 'A', 'A', 'A', 'A', 'A', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '夏はびしょ濡れ需要でピーク長め',
                ],
                [
                    'id' => 'big-thunder',
                    'name' => 'ビッグサンダー・マウンテン',
                    'duration' => '約4分',
                    'area' => 'ウエスタンランド',
                    'rank' => 'A',
                    'pid' => 160,
                    'icon' => 'coaster',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/461_thum_name.jpg',
                    'wait' => [15, 40, 55, 60, 60, 55, 55, 50, 45, 40, 35, 25, 20],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-'],
                    'tip' => 'プライオリティパス対象。開園直後にPP取得→午前中に消化が効率的',
                ],
                [
                    'id' => 'haunted',
                    'name' => 'ホーンテッドマンション',
                    'duration' => '約15分',
                    'area' => 'ファンタジーランド',
                    'rank' => 'A',
                    'pid' => 171,
                    'icon' => 'ghost',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/611_thum_name.jpg',
                    'wait' => [13, 30, 40, 45, 45, 40, 40, 35, 30, 30, 25, 20, 15],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'PP対象。回転が速く見た目より進む。8/4から長期休止に入るので夏前半が乗り時',
                ],
                [
                    'id' => 'star-tours',
                    'name' => 'スター・ツアーズ：ザ・アドベンチャーズ・コンティニュー',
                    'duration' => '約4分30秒',
                    'area' => 'トゥモローランド',
                    'rank' => 'A',
                    'pid' => 183,
                    'icon' => 'space',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/311_thum_name.jpg',
                    'wait' => [10, 25, 35, 40, 40, 35, 35, 30, 25, 20, 20, 13, 10],
                    'pass' => ['P', 'P', 'P', 'P', 'P', 'P', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'PP対象・屋内で涼しい。展開ランダムで連続乗車も楽しい',
                ],
                [
                    'id' => 'gadget',
                    'name' => 'ガジェットのゴーコースター',
                    'duration' => '約1分',
                    'area' => 'トゥーンタウン',
                    'rank' => 'B',
                    'pid' => 179,
                    'icon' => 'splash',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/81_thum_name.jpg',
                    'wait' => [13, 30, 40, 45, 45, 40, 40, 35, 30, 25, 20, 15, 10],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '短いコースターなので回転は速い',
                ],
                [
                    'id' => 'peter-pan',
                    'name' => 'ピーターパン空の旅',
                    'duration' => '約2分30秒',
                    'area' => 'ファンタジーランド',
                    'rank' => 'B',
                    'pid' => 164,
                    'icon' => 'ship',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/521_thum_name.jpg',
                    'wait' => [15, 30, 40, 45, 40, 40, 35, 35, 30, 25, 20, 15, 13],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '定番の割に列が伸びやすい。17:00のパレード中が狙い目',
                ],
                [
                    'id' => 'jungle-cruise',
                    'name' => 'ジャングルクルーズ：ワイルドライフ・エクスペディション',
                    'duration' => '約10分',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'B',
                    'pid' => 153,
                    'icon' => 'boat',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/231_thum_name.jpg',
                    'wait' => [10, 25, 35, 40, 40, 35, 35, 30, 25, 25, 20, 15, 10],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'ナイトクルーズは雰囲気抜群。日中の炎天下は避けたい',
                ],
                [
                    'id' => 'pirates',
                    'name' => 'カリブの海賊',
                    'duration' => '約15分',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'B',
                    'pid' => 152,
                    'icon' => 'boat',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/51_thum_name.jpg',
                    'wait' => [5, 15, 25, 30, 30, 25, 25, 20, 15, 15, 13, 10, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '涼しい屋内ボートライド。昼ピークの休憩に最適',
                ],
                [
                    'id' => 'roger-rabbit',
                    'name' => 'ロジャーラビットのカートゥーンスピン',
                    'duration' => '約3分30秒',
                    'area' => 'トゥーンタウン',
                    'rank' => 'B',
                    'pid' => 175,
                    'icon' => 'spin',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/381_thum_name.jpg',
                    'wait' => [10, 20, 30, 35, 35, 30, 30, 25, 20, 20, 15, 10, 10],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => 'トゥーンタウンの主力ライド。夕方以降にゆるやかに空く',
                ],
                [
                    'id' => 'alice-teaparty',
                    'name' => 'アリスのティーパーティー',
                    'duration' => '約1分30秒',
                    'area' => 'ファンタジーランド',
                    'rank' => 'B',
                    'pid' => 173,
                    'icon' => 'teacup',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/1_thum_name.jpg',
                    'wait' => [5, 15, 25, 30, 30, 25, 25, 20, 20, 15, 13, 10, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '夜はライトアップされたカップが幻想的。回転が速い',
                ],
                [
                    'id' => 'western-river',
                    'name' => 'ウエスタンリバー鉄道',
                    'duration' => '約15分',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'C',
                    'pid' => 154,
                    'icon' => 'train',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/31_thum_name.jpg',
                    'wait' => [5, 10, 15, 20, 20, 15, 15, 15, 13, 10, 10, 5, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '座って回れる休憩系。夕暮れ便がおすすめ。8/4〜8/24は休止予定',
                ],
                [
                    'id' => 'tiki-room',
                    'name' => '魅惑のチキルーム：スティッチ・プレゼンツ',
                    'duration' => '約10分',
                    'area' => 'アドベンチャーランド',
                    'rank' => 'C',
                    'pid' => 156,
                    'icon' => 'bird',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/571_thum_name.jpg',
                    'wait' => [null, 5, 10, 10, 10, 10, 10, 10, 5, 5, 5, null, null],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '冷房×着席。真夏の昼の最強リフレッシュ枠（運営10:00〜20:00）',
                ],
                [
                    'id' => 'stitch-encounter',
                    'name' => 'スティッチ・エンカウンター',
                    'duration' => '約12分',
                    'area' => 'トゥモローランド',
                    'rank' => 'C',
                    'pid' => 195,
                    'icon' => 'ufo',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/681_thum_name.jpg',
                    'wait' => [null, 13, 20, 25, 20, 20, 20, 15, 13, 10, null, null, null],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '屋内シアターで涼しい。毎回違うおしゃべりが楽しめる（運営10:00〜19:00）',
                ],
                [
                    'id' => 'philharmagic',
                    'name' => 'ミッキーのフィルハーマジック',
                    'duration' => '約16分',
                    'area' => 'ファンタジーランド',
                    'rank' => 'C',
                    'pid' => 167,
                    'icon' => 'music',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/images/adventure/attraction/641_thum_name.jpg',
                    'wait' => [5, 13, 20, 20, 20, 20, 20, 15, 13, 13, 10, 5, 5],
                    'pass' => ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'],
                    'tip' => '3Dシアターで涼みながら名曲を。昼ピークの避難先に最適',
                ],
            ],
        ];
    }

    /**
     * フードメニュー（当面モック）
     * @return array<string, mixed>
     */
    private static function food(): array
    {
        return [
            'notes' => [
                '夏イベント限定メニューは「サマー・クールオフ at Tokyo Disney Resort」期間（〜9/14頃）の販売です。価格・内容・販売店舗は予告なく変更される場合があります。',
                '売切れ・販売休止・モバイルオーダー対応状況は、当日東京ディズニーリゾート・アプリのメニュー検索で確認するのがおすすめです。',
                '出典：東京ディズニーリゾート公式サイト おすすめメニュー（2026年7月時点）。',
            ],
            'items' => [
                [
                    'id' => 'baymax-curry',
                    'category' => 'summer',
                    'icon' => 'curry',
                    'name' => 'ベイマックス・プレート（チキンカレー）',
                    'price' => '¥1,580',
                    'area' => 'ワールドバザール',
                    'shop' => 'センターストリート・コーヒーハウス',
                    'note' => 'チキンカレー、芋もちのフライ、ブロッコリー。ベイマックスの顔が目印',
                    'timeLimit' => '',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/food_menu/image/4692_1.0_1_064C97vL.jpg',
                ],
                [
                    'id' => 'baymax-stroganoff',
                    'category' => 'summer',
                    'icon' => 'plate',
                    'name' => 'ベイマックス・プレート（ビーフストロガノフ）',
                    'price' => '¥1,780',
                    'area' => 'ワールドバザール',
                    'shop' => 'センターストリート・コーヒーハウス',
                    'note' => 'ビーフストロガノフ、芋もちのフライ、ブロッコリー',
                    'timeLimit' => '',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/food_menu/image/4693_1.0_1_u291o7K2.jpg',
                ],
                [
                    'id' => 'sirloin-steak',
                    'category' => 'summer',
                    'icon' => 'steak',
                    'name' => 'ステーキプレート',
                    'price' => '¥3,380',
                    'area' => 'ワールドバザール',
                    'shop' => 'センターストリート・コーヒーハウス',
                    'note' => 'ラタトゥイユ、フレンチフライポテト付き。ガッツリ補給に（〜9/14）',
                    'timeLimit' => '',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/food_menu/image/4701_1.0_1_8Tt15KJ6.jpg',
                ],
                [
                    'id' => 'original-parfait',
                    'category' => 'summer',
                    'icon' => 'parfait',
                    'name' => 'オリジナルパフェ（ココナッツムース、シェイブアイス、もちアイスほか）',
                    'price' => '¥1,580',
                    'area' => 'ワールドバザール',
                    'shop' => 'センターストリート・コーヒーハウス',
                    'note' => 'マンゴー・ピーチ・杏仁豆腐・わらび餅まで入った贅沢パフェ',
                    'timeLimit' => '14:00〜17:00限定',
                    'thumbUrl' => 'https://media1.tokyodisneyresort.jp/food_menu/image/4756_1.0_1_956nt13n.jpg',
                ],
            ],
        ];
    }
}
