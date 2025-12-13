<?php

namespace App\Apis\Events;

use App\Http\Controllers\Controller;
use App\Repositories\MstTalentRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventRepository;
use Illuminate\Http\JsonResponse;

class EventsController extends Controller
{
    private $mstTalentRepository;
    private $tblEventRepository;
    private $tblEventCastTalentRepository;

    public function __construct()
    {
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
        $mstTalents = $this->mstTalentRepository->all();
        $tblEvents = $this->tblEventRepository->all();
        $tblEventCastTalents = $this->tblEventCastTalentRepository->all();
        $responseData = [
            'success' => true,
            'data' => $tblEvents->map(function ($tblEvent) use ($tblEventCastTalents, $mstTalents) {
                $talentIds = $tblEventCastTalents->where('event_id', $tblEvent->id)->pluck('talent_id')->toArray();
                $talentNames = $mstTalents->whereIn('id', $talentIds)->pluck('talent_name')->toArray();
                return [
                    'id' => $tblEvent->id,
                    'title' => $tblEvent->event_name,
                    'date' => $tblEvent->event_start_date,
                    'endDate' => $tblEvent->event_end_date,
                    'startTime' => $tblEvent->start_time,
                    'endTime' => $tblEvent->end_time,
                    'type' => 'goods',
                    'talentNames' => $talentNames,
                    'description' => $tblEvent->description,
                    'color' => '#1E90FF',
                    'url' => $tblEvent->event_url,
                    'thumbnailUrl' => 'https://placehold.co/800x400/1E90FF/FFFFFF?text=POP+UP+SHOP',
                    'location' => $tblEvent->location,
                    'notes' => [
                        '※1月22日は18:00までの営業となります。',
                        '※一部日時は事前抽選による予約制となります。',
                    ],
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
        $mstTalents = $this->mstTalentRepository->all();
        $tblEvent = $this->tblEventRepository->findPk($id);
        $tblEventCastTalents = $this->tblEventCastTalentRepository->all();

        $talentIds = $tblEventCastTalents->where('event_id', $tblEvent->id)->pluck('talent_id')->toArray();
        $talentNames = $mstTalents->whereIn('id', $talentIds)->pluck('talent_name')->toArray();

        $responseData = [
            'success' => true,
            'data' => [
                'id' => $tblEvent->id,
                'title' => $tblEvent->event_name,
                'date' => $tblEvent->event_start_date,
                'endDate' => $tblEvent->event_end_date,
                'startTime' => $tblEvent->start_time,
                'endTime' => $tblEvent->end_time,
                'type' => 'goods',
                'talentNames' => $talentNames,
                'description' => $tblEvent->description,
                'color' => '#1E90FF',
                'url' => $tblEvent->event_url,
                'thumbnailUrl' => 'https://placehold.co/800x400/1E90FF/FFFFFF?text=POP+UP+SHOP',
                'location' => $tblEvent->location,
                'notes' => [
                    '※1月22日は18:00までの営業となります。',
                    '※一部日時は事前抽選による予約制となります。',
                ],
                'status' => 'draft',
                'createdAt' => $tblEvent->created_datetime,
                'updatedAt' => $tblEvent->updated_datetime,
            ],
        ];

        return response()->json($responseData);
    }
}
