<?php

namespace App\Apis\Events;

use App\Http\Controllers\Controller;
use App\Repositories\MstEventTypeRepository;
use App\Repositories\MstTalentRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    private $mstEventTypeRepository;
    private $mstTalentRepository;
    private $tblEventRepository;
    private $tblEventCastTalentRepository;

    public function __construct()
    {
        $this->mstEventTypeRepository = new MstEventTypeRepository();
        $this->mstTalentRepository = new MstTalentRepository();
        $this->tblEventRepository = new TblEventRepository();
        $this->tblEventCastTalentRepository = new TblEventCastTalentRepository();
    }

    /**
     * イベント一覧取得API
     * GET /events
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $mstEventTypes = $this->mstEventTypeRepository->all();
        $mstTalents = $this->mstTalentRepository->all();
        $tblEvents = $this->tblEventRepository->all();
        $tblEventCastTalents = $this->tblEventCastTalentRepository->all();
        $responseData = [
            'success' => true,
            'data' => $tblEvents->map(function ($tblEvent) use ($tblEventCastTalents, $mstTalents, $mstEventTypes) {
                $eventType = $mstEventTypes->where('id', $tblEvent->event_type_id)->first();
                $talentIds = $tblEventCastTalents->where('event_id', $tblEvent->id)->pluck('talent_id')->toArray();
                $talentNames = $mstTalents->whereIn('id', $talentIds)->pluck('talent_name')->toArray();

                $notes = [];
                if($tblEvent->note) {
                    $notes = [$tblEvent->note];
                }
                return [
                    'id' => $tblEvent->id,
                    'title' => $tblEvent->event_name,
                    'date' => $tblEvent->event_start_date,
                    'endDate' => $tblEvent->event_end_date,
                    'startTime' => $tblEvent->start_time,
                    'endTime' => $tblEvent->end_time,
                    'type' => $eventType->event_type_name,
                    'talentNames' => $talentNames,
                    'description' => $tblEvent->description,
                    'color' => $eventType->event_color_code,
                    'url' => $tblEvent->event_url,
                    'thumbnailUrl' => $eventType->thumbnail_img_url,
                    'location' => $tblEvent->location,
                    'notes' => $notes,
                    'status' => 'draft',
                    'createdAt' => $tblEvent->created_datetime,
                    'updatedAt' => $tblEvent->updated_datetime,
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
        // IDに該当するイベントを検索
        $mstEventTypes = $this->mstEventTypeRepository->all();
        $mstTalents = $this->mstTalentRepository->all();
        $tblEvent = $this->tblEventRepository->findPk($id);
        $tblEventCastTalents = $this->tblEventCastTalentRepository->all();

        $eventType = $mstEventTypes->where('id', $tblEvent->event_type_id)->first();
        $talentIds = $tblEventCastTalents->where('event_id', $tblEvent->id)->pluck('talent_id')->toArray();
        $talentNames = $mstTalents->whereIn('id', $talentIds)->pluck('talent_name')->toArray();

        $notes = [];
        if($tblEvent->note) {
            $notes = [$tblEvent->note];
        }

        $responseData = [
            'success' => true,
            'data' => [
                'id' => $tblEvent->id,
                'title' => $tblEvent->event_name,
                'date' => $tblEvent->event_start_date,
                'endDate' => $tblEvent->event_end_date,
                'startTime' => $tblEvent->start_time,
                'endTime' => $tblEvent->end_time,
                'type' => $eventType->event_type_name,
                'talentNames' => $talentNames,
                'description' => $tblEvent->description,
                'color' => $eventType->event_color_code,
                'url' => $tblEvent->event_url,
                'thumbnailUrl' => $eventType->thumbnail_img_url,
                'location' => $tblEvent->location,
                'notes' => $notes,
                'status' => 'draft',
                'createdAt' => $tblEvent->created_datetime,
                'updatedAt' => $tblEvent->updated_datetime,
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
            'events.*.event_type_id' => 'required|integer|exists:mst_event_type,id',
            'events.*.description' => 'nullable|string',
            'events.*.location' => 'nullable|string|max:255',
            'events.*.address' => 'nullable|string|max:255',
            'events.*.latitude' => 'nullable|numeric',
            'events.*.longitude' => 'nullable|numeric',
            'events.*.station' => 'nullable|string|max:255',
            'events.*.event_url' => 'nullable|url|max:255',
            'events.*.note' => 'nullable|string',
            'events.*.talent_ids' => 'nullable|array',
            'events.*.talent_ids.*' => 'integer|exists:mst_talent,id',
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
            foreach ($request->events as $index => $eventData) {
                try {
                    // イベントデータをオブジェクトに変換
                    $eventObject = (object) $eventData;

                    // イベントを登録
                    $event = $this->tblEventRepository->insert($eventObject);

                    // タレント情報が存在する場合は関連付けを登録
                    if (isset($eventData['talent_ids']) && is_array($eventData['talent_ids'])) {
                        foreach ($eventData['talent_ids'] as $talentId) {
                            $castTalentObject = (object) [
                                'event_id' => $event->id,
                                'talent_id' => $talentId,
                                'created_program_name' => 'API',
                                'updated_program_name' => 'API',
                            ];
                            $this->tblEventCastTalentRepository->insert($castTalentObject);
                        }
                    }

                    $registeredEvents[] = [
                        'index' => $index,
                        'id' => $event->id,
                        'event_name' => $event->event_name,
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
