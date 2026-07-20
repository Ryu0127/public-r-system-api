<?php

namespace App\Apis\Showtimes;

use App\Apis\Showtimes\Mock\TdlShowtimesMock;
use App\Contexts\Application\Services\Showtimes\DisneyParkAttractionApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkAttractionWaitForecastApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkFoodMenuApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkFoodMenuContentApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkFoodShopApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkShowParadeApplicationService;
use App\Contexts\Application\Services\Showtimes\DisneyParkShowParadeScheduleApplicationService;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkAttractionWaitForecastAggregateList;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowtimesController extends Controller
{
    private DisneyParkShowParadeApplicationService $disneyParkShowParadeApplicationService;
    private DisneyParkShowParadeScheduleApplicationService $disneyParkShowParadeScheduleApplicationService;
    private DisneyParkAttractionApplicationService $disneyParkAttractionApplicationService;
    private DisneyParkAttractionWaitForecastApplicationService $disneyParkAttractionWaitForecastApplicationService;
    private DisneyParkFoodShopApplicationService $disneyParkFoodShopApplicationService;
    private DisneyParkFoodMenuApplicationService $disneyParkFoodMenuApplicationService;
    private DisneyParkFoodMenuContentApplicationService $disneyParkFoodMenuContentApplicationService;

    /** park_type: 1=ランド */
    private const PARK_TYPE_LAND = 1;

    /** show_parade_type ラベル */
    private const SHOW_PARADE_TYPE_LABELS = [
        1 => 'ステージショー',
        2 => 'パレード',
        3 => 'キャッスルショー',
        4 => '花火',
    ];

    /** area_type ラベル（TDL） */
    private const AREA_TYPE_LABELS = [
        1 => 'ワールドバザール',
        2 => 'アドベンチャーランド',
        3 => 'ウエスタンランド',
        4 => 'クリッターカントリー',
        5 => 'ファンタジーランド',
        6 => 'トゥーンタウン',
        7 => 'トゥモローランド',
    ];

    /** rank_type ラベル */
    private const RANK_TYPE_LABELS = [
        1 => 'S',
        2 => 'A',
        3 => 'B',
        4 => 'C',
    ];

    /** shop_type ラベル */
    private const SHOP_TYPE_LABELS = [
        1 => 'クイックサービス',
        2 => 'レストラン',
        3 => 'ワゴン・カート',
        4 => 'ラウンジ・バー',
    ];

    /** category_type ラベル（フードメニュー） */
    private const FOOD_MENU_CATEGORY_TYPE_LABELS = [
        1 => '定番',
        2 => '期間限定',
    ];

    public function __construct(
        DisneyParkShowParadeApplicationService $disneyParkShowParadeApplicationService,
        DisneyParkShowParadeScheduleApplicationService $disneyParkShowParadeScheduleApplicationService,
        DisneyParkAttractionApplicationService $disneyParkAttractionApplicationService,
        DisneyParkAttractionWaitForecastApplicationService $disneyParkAttractionWaitForecastApplicationService,
        DisneyParkFoodShopApplicationService $disneyParkFoodShopApplicationService,
        DisneyParkFoodMenuApplicationService $disneyParkFoodMenuApplicationService,
        DisneyParkFoodMenuContentApplicationService $disneyParkFoodMenuContentApplicationService
    ) {
        $this->disneyParkShowParadeApplicationService = $disneyParkShowParadeApplicationService;
        $this->disneyParkShowParadeScheduleApplicationService = $disneyParkShowParadeScheduleApplicationService;
        $this->disneyParkAttractionApplicationService = $disneyParkAttractionApplicationService;
        $this->disneyParkAttractionWaitForecastApplicationService = $disneyParkAttractionWaitForecastApplicationService;
        $this->disneyParkFoodShopApplicationService = $disneyParkFoodShopApplicationService;
        $this->disneyParkFoodMenuApplicationService = $disneyParkFoodMenuApplicationService;
        $this->disneyParkFoodMenuContentApplicationService = $disneyParkFoodMenuContentApplicationService;
    }

    /**
     * TDL ショー&パレード / 混雑データ取得API（モック）
     * GET /showtimes/tdl?date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function tdl(Request $request): JsonResponse
    {
        $date = $request->query('date', '2026-07-21');

        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        return response()->json([
            'status' => true,
            'data' => TdlShowtimesMock::forDate($date),
        ]);
    }

    /**
     * ショー・パレード一覧取得API（マスタ＋公演時刻）
     * GET /showtimes/show-parades?park_type=1&date=2026-07-21
     * GET /showtimes/show-parades?park_type=1&include_canceled=1
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function showParades(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');
        $includeCanceled = $request->query('include_canceled', '0');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $aggregateList = $this->disneyParkShowParadeApplicationService
            ->selectByParkType((int) $parkType);

        if (is_string($date)) {
            $aggregateList = $aggregateList->filterPublishedOn($date);
        }

        $showParadeIds = array_map('intval', $aggregateList->getIds());
        $scheduleAggregateList = $this->disneyParkShowParadeScheduleApplicationService
            ->selectByShowParadeIds($showParadeIds);

        if ((string) $includeCanceled !== '1') {
            $scheduleAggregateList = $scheduleAggregateList->filterNotCanceled();
        }

        $schedulesByShowParadeId = [];
        foreach ($scheduleAggregateList->getAggregates() as $scheduleAggregate) {
            $scheduleEntity = $scheduleAggregate->getEntity();
            $showParadeId = (int) $scheduleEntity->disney_park_show_parade_id;
            if (!isset($schedulesByShowParadeId[$showParadeId])) {
                $schedulesByShowParadeId[$showParadeId] = [];
            }
            $schedulesByShowParadeId[$showParadeId][] = [
                'id' => $scheduleEntity->id,
                'startTime' => $scheduleEntity->start_time,
                'note' => $scheduleEntity->note,
                'cancelFlag' => (int) $scheduleEntity->cancel_flag,
            ];
        }

        $showParades = $aggregateList->getAggregates()->map(function ($aggregate) use ($schedulesByShowParadeId) {
            $entity = $aggregate->getEntity();
            $type = (int) $entity->show_parade_type;
            $id = (int) $entity->id;

            return [
                'id' => $id,
                'parkType' => (int) $entity->park_type,
                'showParadeType' => $type,
                'showParadeTypeLabel' => self::SHOW_PARADE_TYPE_LABELS[$type] ?? null,
                'showParadeName' => $entity->show_parade_name,
                'durationMinutes' => $entity->duration_minutes !== null
                    ? (int) $entity->duration_minutes
                    : null,
                'thumbUrl' => $entity->thumb_url,
                'publishStartDate' => $entity->publish_start_date,
                'publishEndDate' => $entity->publish_end_date,
                'entryFlag' => (int) $entity->entry_flag,
                'dpaFlag' => (int) $entity->dpa_flag,
                'pauseFlag' => (int) $entity->pause_flag,
                'schedules' => $schedulesByShowParadeId[$id] ?? [],
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'showParades' => $showParades,
            ],
        ]);
    }

    /**
     * アトラクション一覧取得API（マスタ）
     * GET /showtimes/attractions?park_type=1&date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function attractions(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $aggregateList = $this->disneyParkAttractionApplicationService
            ->selectByParkType((int) $parkType);

        if (is_string($date)) {
            $aggregateList = $aggregateList->filterPublishedOn($date);
        }

        $attractions = $aggregateList->getAggregates()->map(function ($aggregate) {
            $entity = $aggregate->getEntity();
            $areaType = (int) $entity->area_type;
            $rankType = (int) $entity->rank_type;

            return [
                'id' => (int) $entity->id,
                'parkType' => (int) $entity->park_type,
                'attractionName' => $entity->attraction_name,
                'areaType' => $areaType,
                'areaTypeLabel' => self::AREA_TYPE_LABELS[$areaType] ?? null,
                'rankType' => $rankType,
                'rankTypeLabel' => self::RANK_TYPE_LABELS[$rankType] ?? null,
                'durationMinutes' => $entity->duration_minutes !== null
                    ? (int) $entity->duration_minutes
                    : null,
                'thumbUrl' => $entity->thumb_url,
                'publishStartDate' => $entity->publish_start_date,
                'publishEndDate' => $entity->publish_end_date,
                'dpaFlag' => (int) $entity->dpa_flag,
                'priorityPassFlag' => (int) $entity->priority_pass_flag,
                'pauseFlag' => (int) $entity->pause_flag,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'attractions' => $attractions,
            ],
        ]);
    }

    /**
     * アトラクション時間帯別予想待ち時間取得API
     * GET /showtimes/attraction-wait-forecasts?park_type=1&date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function attractionWaitForecasts(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $attractionAggregateList = $this->disneyParkAttractionApplicationService
            ->selectByParkType((int) $parkType)
            ->filterPublishedOn($date);

        $attractionIds = array_map('intval', $attractionAggregateList->getIds());
        $forecastAggregateList = $this->disneyParkAttractionWaitForecastApplicationService
            ->selectByDisneyParkAttractionIdsAndTargetDate($attractionIds, $date);

        $slots = $forecastAggregateList->getSlotTimes();
        $waitsByAttractionId = $forecastAggregateList->groupWaitMinutesByAttractionId($slots);

        $forecasts = $forecastAggregateList->getAggregates()->map(function ($aggregate) {
            $entity = $aggregate->getEntity();

            return [
                'id' => (int) $entity->id,
                'disneyParkAttractionId' => (int) $entity->disney_park_attraction_id,
                'targetDate' => $entity->target_date instanceof \DateTimeInterface
                    ? $entity->target_date->format('Y-m-d')
                    : (string) $entity->target_date,
                'slotTime' => DisneyParkAttractionWaitForecastAggregateList::formatSlotTime(
                    $entity->slot_time
                ),
                'waitMinutes' => $entity->wait_minutes !== null
                    ? (int) $entity->wait_minutes
                    : null,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'slots' => $slots,
                'waitsByAttractionId' => (object) $waitsByAttractionId,
                'forecasts' => $forecasts,
            ],
        ]);
    }

    /**
     * フード販売ショップ一覧取得API（マスタ）
     * GET /showtimes/food-shops?park_type=1&date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function foodShops(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $aggregateList = $this->disneyParkFoodShopApplicationService
            ->selectByParkType((int) $parkType);

        if (is_string($date)) {
            $aggregateList = $aggregateList->filterPublishedOn($date);
        }

        $foodShops = $aggregateList->getAggregates()->map(function ($aggregate) {
            $entity = $aggregate->getEntity();
            $areaType = (int) $entity->area_type;
            $shopType = (int) $entity->shop_type;

            return [
                'id' => (int) $entity->id,
                'parkType' => (int) $entity->park_type,
                'shopName' => $entity->shop_name,
                'areaType' => $areaType,
                'areaTypeLabel' => self::AREA_TYPE_LABELS[$areaType] ?? null,
                'shopType' => $shopType,
                'shopTypeLabel' => self::SHOP_TYPE_LABELS[$shopType] ?? null,
                'thumbUrl' => $entity->thumb_url,
                'mobileOrderFlag' => (int) $entity->mobile_order_flag,
                'reservationFlag' => (int) $entity->reservation_flag,
                'publishStartDate' => $entity->publish_start_date,
                'publishEndDate' => $entity->publish_end_date,
                'pauseFlag' => (int) $entity->pause_flag,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'foodShops' => $foodShops,
            ],
        ]);
    }

    /**
     * フードメニュー一覧取得API（マスタ）
     * GET /showtimes/food-menus?park_type=1&date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function foodMenus(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $shopAggregateList = $this->disneyParkFoodShopApplicationService
            ->selectByParkType((int) $parkType);

        if (is_string($date)) {
            $shopAggregateList = $shopAggregateList->filterPublishedOn($date);
        }

        $shopById = [];
        foreach ($shopAggregateList->getAggregates() as $shopAggregate) {
            $shopEntity = $shopAggregate->getEntity();
            $shopById[(int) $shopEntity->id] = $shopEntity;
        }

        $shopIds = array_map('intval', array_keys($shopById));
        $menuAggregateList = $this->disneyParkFoodMenuApplicationService
            ->selectByDisneyParkFoodShopIds($shopIds);

        if (is_string($date)) {
            $menuAggregateList = $menuAggregateList->filterPublishedOn($date);
        }

        $menuIds = array_map('intval', $menuAggregateList->getIds());
        $contentAggregateList = $this->disneyParkFoodMenuContentApplicationService
            ->selectByDisneyParkFoodMenuIds($menuIds);
        $contentsByMenuId = $contentAggregateList->groupContentsByMenuId();

        $foodMenus = $menuAggregateList->getAggregates()->map(function ($aggregate) use ($shopById, $contentsByMenuId) {
            $entity = $aggregate->getEntity();
            $shopId = (int) $entity->disney_park_food_shop_id;
            $menuId = (int) $entity->id;
            $shop = $shopById[$shopId] ?? null;
            $areaType = $shop !== null ? (int) $shop->area_type : null;
            $categoryType = (int) $entity->category_type;

            return [
                'id' => $menuId,
                'disneyParkFoodShopId' => $shopId,
                'menuName' => $entity->menu_name,
                'categoryType' => $categoryType,
                'categoryTypeLabel' => self::FOOD_MENU_CATEGORY_TYPE_LABELS[$categoryType] ?? null,
                'priceYen' => $entity->price_yen !== null ? (int) $entity->price_yen : null,
                'note' => $entity->note,
                'timeLimitNote' => $entity->time_limit_note,
                'thumbUrl' => $entity->thumb_url,
                'officialUrl' => $entity->official_url,
                'publishStartDate' => $entity->publish_start_date,
                'publishEndDate' => $entity->publish_end_date,
                'pauseFlag' => (int) $entity->pause_flag,
                'shopName' => $shop !== null ? $shop->shop_name : null,
                'areaType' => $areaType,
                'areaTypeLabel' => $areaType !== null
                    ? (self::AREA_TYPE_LABELS[$areaType] ?? null)
                    : null,
                'contents' => $contentsByMenuId[$menuId] ?? [],
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'foodMenus' => $foodMenus,
            ],
        ]);
    }

    /**
     * フードメニュー内容一覧取得API（マスタ）
     * GET /showtimes/food-menu-contents?park_type=1&date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function foodMenuContents(Request $request): JsonResponse
    {
        $parkType = $request->query('park_type', self::PARK_TYPE_LAND);
        $date = $request->query('date');

        if (!is_numeric($parkType) || !in_array((int) $parkType, [1, 2], true)) {
            return response()->json([
                'status' => false,
                'message' => 'park_type は 1（ランド）または 2（シー）で指定してください',
            ], 400);
        }

        if ($date !== null && (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        $shopAggregateList = $this->disneyParkFoodShopApplicationService
            ->selectByParkType((int) $parkType);

        if (is_string($date)) {
            $shopAggregateList = $shopAggregateList->filterPublishedOn($date);
        }

        $shopIds = array_map('intval', $shopAggregateList->getIds());
        $menuAggregateList = $this->disneyParkFoodMenuApplicationService
            ->selectByDisneyParkFoodShopIds($shopIds);

        if (is_string($date)) {
            $menuAggregateList = $menuAggregateList->filterPublishedOn($date);
        }

        $menuIds = array_map('intval', $menuAggregateList->getIds());
        $contentAggregateList = $this->disneyParkFoodMenuContentApplicationService
            ->selectByDisneyParkFoodMenuIds($menuIds);

        $foodMenuContents = $contentAggregateList->getAggregates()->map(function ($aggregate) {
            $entity = $aggregate->getEntity();

            return [
                'id' => (int) $entity->id,
                'disneyParkFoodMenuId' => (int) $entity->disney_park_food_menu_id,
                'contentName' => $entity->content_name,
                'pauseFlag' => (int) $entity->pause_flag,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => [
                'foodMenuContents' => $foodMenuContents,
            ],
        ]);
    }
}
