<?php

namespace App\Apis\Events;

use App\Contexts\Domain\Aggregates\EventTypeAggregate;
use App\Contexts\Application\Services\Talent\EventApplicationService;
use App\Contexts\Domain\Aggregates\EventAggregate;
use App\Contexts\Domain\Aggregates\EventCastTalentAggregate;
use App\Http\Controllers\Controller;
use App\Models\MstEvent;
use App\Models\MstEventType;
use App\Models\TblEvent;
use App\Models\TblEventCastTalent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    private $eventApplicationService;

    public function __construct(
        EventApplicationService $eventApplicationService,
    ) {
        $this->eventApplicationService = $eventApplicationService;
    }

    /**
     * イベント一覧取得API
     * GET /events
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // select
        $eventAggregateList = $this->eventApplicationService->selectEvent();
        $eventTypeAggregateList = $this->eventApplicationService->selectEventType();
        $talentAggregateList = $this->eventApplicationService->selectTalent();
        $eventCastTalentAggregateList = $this->eventApplicationService->selectEventCastTalent();
        // response
        $eventAggregates = $eventAggregateList->getAggregates();
        $responseData = [
            'success' => true,
            'data' => $eventAggregates->map(function ($eventAggregate) use ($eventCastTalentAggregateList, $talentAggregateList, $eventTypeAggregateList) {
                $eventTypeAggregate = $eventTypeAggregateList->firstById($eventAggregate->getEntity()->event_type_id) ?? new EventTypeAggregate(new MstEventType());
                $eventCastTalentAggregateList = $eventCastTalentAggregateList->filterByEventId($eventAggregate->getEntity()->id);
                $filteredTalentAggregateList = $talentAggregateList->filterById($eventCastTalentAggregateList->getTalentIds());

                $notes = [];
                if($eventAggregate->getEntity()->note) {
                    $notes = [$eventAggregate->getEntity()->note];
                }
                return [
                    'id' => $eventAggregate->getEntity()->id,
                    'title' => $eventAggregate->getEntity()->event_name,
                    'date' => $eventAggregate->getEntity()->event_start_date,
                    'endDate' => $eventAggregate->getEntity()->event_end_date,
                    'startTime' => $eventAggregate->getEntity()->start_time,
                    'endTime' => $eventAggregate->getEntity()->end_time,
                    'type' => $eventTypeAggregate->getEntity()->event_type_name,
                    'talentNames' => $filteredTalentAggregateList->getTalentNames(),
                    'description' => $eventAggregate->getEntity()->description,
                    'color' => $eventTypeAggregate->getEntity()->event_color_code,
                    'url' => $eventAggregate->getEntity()->event_url,
                    'thumbnailUrl' => $eventTypeAggregate->getEntity()->thumbnail_img_url,
                    'location' => $eventAggregate->getEntity()->location,
                    'notes' => $notes,
                    'status' => 'draft',
                    'createdAt' => $eventAggregate->getEntity()->created_datetime,
                    'updatedAt' => $eventAggregate->getEntity()->updated_datetime,
                ];
            }),
            'message' => 'イベント一覧を取得しました',
        ];

        return response()->json($responseData);
    }

    /**
     * イベント詳細取得API
     * GET /events/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // select
        $eventAggregate = $this->eventApplicationService->firstEventById($id);
        $eventTypeAggregateList = $this->eventApplicationService->selectEventType();
        $talentAggregateList = $this->eventApplicationService->selectTalent();
        $eventCastTalentAggregateList = $this->eventApplicationService->selectEventCastTalent();
        // filter
        $eventTypeAggregate = $eventTypeAggregateList->firstById($eventAggregate->getEntity()->event_type_id) ?? new EventTypeAggregate(new MstEventType());
        $eventCastTalentAggregateList = $eventCastTalentAggregateList->filterByEventId($eventAggregate->getEntity()->id);
        $filteredTalentAggregateList = $talentAggregateList->filterById($eventCastTalentAggregateList->getTalentIds());
        $notes = $eventAggregate->getEntity()->note ? [$eventAggregate->getEntity()->note] : [];
        // response
        $responseData = [
            'success' => true,
            'data' => [
                'id' => $eventAggregate->getEntity()->id,
                'title' => $eventAggregate->getEntity()->event_name,
                'date' => $eventAggregate->getEntity()->event_start_date,
                'endDate' => $eventAggregate->getEntity()->event_end_date,
                'startTime' => $eventAggregate->getEntity()->start_time,
                'endTime' => $eventAggregate->getEntity()->end_time,
                'type' => $eventTypeAggregate->getEntity()->event_type_name,
                'talentNames' => $filteredTalentAggregateList->getTalentNames(),
                'description' => $eventAggregate->getEntity()->description,
                'color' => $eventTypeAggregate->getEntity()->event_color_code,
                'url' => $eventAggregate->getEntity()->event_url,
                'thumbnailUrl' => $eventTypeAggregate->getEntity()->thumbnail_img_url,
                'location' => $eventAggregate->getEntity()->location,
                'notes' => $notes,
                'status' => 'draft',
                'createdAt' => $eventAggregate->getEntity()->created_datetime,
                'updatedAt' => $eventAggregate->getEntity()->updated_datetime,
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * イベント登録API（複数件対応）
     * POST /events
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // リクエストデータのバリデーション
        $validator = Validator::make($request->all(), [
            'events' => 'required|array|min:1',
            'events.*.event_name' => 'required|string|max:255',
            'events.*.event_start_date' => 'required|date',
            'events.*.event_end_date' => 'nullable|date|after_or_equal:events.*.event_start_date',
            'events.*.start_time' => 'nullable|date_format:H:i:s',
            'events.*.end_time' => 'nullable|date_format:H:i:s',
            'events.*.event_type_name' => 'required|string|max:255',
            'events.*.description' => 'nullable|string',
            'events.*.location' => 'nullable|string|max:255',
            'events.*.address' => 'nullable|string|max:255',
            'events.*.latitude' => 'nullable|numeric',
            'events.*.longitude' => 'nullable|numeric',
            'events.*.station' => 'nullable|string|max:255',
            'events.*.event_url' => 'nullable|url|max:255',
            'events.*.note' => 'nullable|string',
            'events.*.talent_names' => 'nullable|array',
            'events.*.talent_names.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラーが発生しました',
                'errors' => $validator->errors(),
            ], 422);
        }

        $registeredEvents = [];
        $errors = [];

        DB::beginTransaction();
        try {
            // マスターデータを事前に取得
            $eventTypeAggregateList = $this->eventApplicationService->selectEventType();
            $talentAggregateList = $this->eventApplicationService->selectTalent();

            foreach ($request->events as $index => $eventData) {
                try {
                    $requestData = [
                        'event_name' => $eventData['event_name'] ?? null,
                        'event_start_date' => $eventData['event_start_date'] ?? null,
                        'event_end_date' => $eventData['event_end_date'] ?? null,
                        'start_time' => $eventData['start_time'] ?? null,
                        'end_time' => $eventData['end_time'] ?? null,
                        'event_type_name' => $eventData['event_type_name'] ?? null,
                        'description' => $eventData['description'] ?? null,
                        'note' => $eventData['note'] ?? null,
                        'location' => $eventData['location'] ?? null,
                        'address' => $eventData['address'] ?? null,
                        'latitude' => $eventData['latitude'] ?? null,
                        'longitude' => $eventData['longitude'] ?? null,
                        'station' => $eventData['station'] ?? null,
                        'event_url' => $eventData['event_url'] ?? null,
                        'thumbnail_img_url' => $eventData['thumbnail_img_url'] ?? null,
                        'talent_names' => $eventData['talent_names'] ?? [],
                    ];
                    // イベントタイプ名からIDを取得
                    $eventTypeAggregates = $eventTypeAggregateList->getAggregates();
                    $eventTypeAggregate = $eventTypeAggregates->first(function ($aggregate) use ($requestData) {
                        return $aggregate->getEntity()->event_type_name === $requestData['event_type_name'];
                    });

                    // イベントデータをオブジェクトに変換
                    $eventEntity = new TblEvent();
                    $eventEntity->event_name = $requestData['event_name'];
                    $eventEntity->event_start_date = $requestData['event_start_date'];
                    $eventEntity->event_end_date = $requestData['event_end_date'];
                    $eventEntity->start_time = $requestData['start_time'];
                    $eventEntity->end_time = $requestData['end_time'];
                    $eventEntity->event_type_id = $eventTypeAggregate ? $eventTypeAggregate->getEntity()->id : null;
                    $eventEntity->description = $requestData['description'];
                    $eventEntity->note = $requestData['note'];
                    $eventEntity->location = $requestData['location'];
                    $eventEntity->address = $requestData['address'];
                    $eventEntity->latitude = $requestData['latitude'];
                    $eventEntity->longitude = $requestData['longitude'];
                    $eventEntity->station = $requestData['station'];
                    $eventEntity->event_url = $requestData['event_url'];
                    $eventEntity->thumbnail_img_url = $requestData['thumbnail_img_url'];
                    $eventEntity->created_datetime = now();
                    $eventEntity->updated_datetime = now();
                    $eventAggregate = new EventAggregate($eventEntity);

                    // イベントを登録
                    $eventAggregate = $this->eventApplicationService->insertEvent($eventAggregate);

                    // タレント名が存在する場合はIDに変換して関連付けを登録
                    if (isset($requestData['talent_names']) && is_array($requestData['talent_names'])) {
                        $talentAggregates = $talentAggregateList->getAggregates();
                        foreach ($requestData['talent_names'] as $talentName) {
                            $talentAggregate = $talentAggregates->first(function ($aggregate) use ($talentName) {
                                return $aggregate->getEntity()->talent_name === $talentName;
                            });
                            if (!$talentAggregate) continue;

                            $castTalentEntity = new TblEventCastTalent();
                            $castTalentEntity->event_id = $eventAggregate->getEntity()->id;
                            $castTalentEntity->talent_id = $talentAggregate->getEntity()->id;
                            $castTalentEntity->created_datetime = now();
                            $castTalentEntity->updated_datetime = now();
                            $castTalentAggregate = new EventCastTalentAggregate($castTalentEntity);
                            $this->eventApplicationService->insertEventCastTalent($castTalentAggregate);
                        }
                    }

                    $registeredEvents[] = [
                        'index' => $index,
                        'id' => $eventAggregate->getEntity()->id,
                        'event_name' => $eventAggregate->getEntity()->event_name,
                    ];
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            // エラーがある場合はロールバック
            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '一部のイベントの登録に失敗しました',
                    'errors' => $errors,
                ], 500);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($registeredEvents) . '件のイベントを登録しました',
                'data' => $registeredEvents,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'イベントの登録に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
