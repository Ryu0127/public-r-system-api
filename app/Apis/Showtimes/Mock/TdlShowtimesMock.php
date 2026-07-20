<?php

namespace App\Apis\Showtimes\Mock;

/**
 * TDL ショー&パレード モックデータ（park / shows / timeline）
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

}
