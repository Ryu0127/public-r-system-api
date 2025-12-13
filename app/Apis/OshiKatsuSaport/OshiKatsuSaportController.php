<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentHashtagRepository;
use App\Repositories\MstEventTypeRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventRepository;
use App\Repositories\TblEventHashtagRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OshiKatsuSaportController extends Controller
{
    private $mstTalentRepository;
    private $mstTalentHashtagRepository;
    private $mstEventTypeRepository;
    private $tblEventCastTalentRepository;
    private $tblEventRepository;
    private $tblEventHashtagRepository;

    public function __construct() {
        $this->mstTalentRepository = new MstTalentRepository();
        $this->mstTalentHashtagRepository = new MstTalentHashtagRepository();
        $this->mstEventTypeRepository = new MstEventTypeRepository();
        $this->tblEventCastTalentRepository = new TblEventCastTalentRepository();
        $this->tblEventRepository = new TblEventRepository();
        $this->tblEventHashtagRepository = new TblEventHashtagRepository();
    }

    /**
     * タレント一覧取得API
     * GET /oshi-katsu-saport/talents
     *
     * @return JsonResponse
     */
    public function talents(): JsonResponse
    {
        $mstTalents = $this->mstTalentRepository->all();
        $responseData = [
            'status' => true,
            'data' => [
                'talents' => $mstTalents->map(function ($mstTalent) {
                    return [
                        'id' => $mstTalent->id,
                        'talentName' => $mstTalent->talent_name,
                        'talentNameEn' => $mstTalent->talent_name_en,
                    ];
                }),
            ],
        ];
        return response()->json($responseData);
    }

    /**
     * タレント別ハッシュタグ取得API
     * GET /oshi-katsu-saport/talents/{id}/hashtags
     *
     * @param string $id
     * @return JsonResponse
     */
    public function talentHashtags(string $id): JsonResponse
    {
        $mstTalent = $this->mstTalentRepository->findPk($id);
        $mstTalentHashtags = $this->mstTalentHashtagRepository->getByTalentId($id);
        $mstEventTypes = $this->mstEventTypeRepository->all();
        $tblEventCastTalents = $this->tblEventCastTalentRepository->getByTalentId($id);
        $tblEvents = $this->tblEventRepository->getByPkIds($tblEventCastTalents->pluck('event_id')->toArray());
        $tblEventHashtags = $this->tblEventHashtagRepository->getByEventIds($tblEvents->pluck('id')->toArray());
        $filteredEvents = $this->filterEventsByEventHashtags($tblEvents, $tblEventHashtags);
        $filteredEvents = $this->filterEventsByNotEndDateOver($filteredEvents, 8);

        $responseData = [
            'status' => true,
            'data' => [
                'talent' => [
                    'id' => $mstTalent->id,
                    'name' => $mstTalent->talent_name,
                ],
                'hashtags' => $mstTalentHashtags->map(function ($mstTalentHashtag) {
                    return [
                        'id' => $mstTalentHashtag->id,
                        'tag' => $mstTalentHashtag->hashtag,
                        'description' => $mstTalentHashtag->description,
                    ];
                })->values(),
                'eventHashtags' => $filteredEvents->map(function ($tblEvent) use ($mstEventTypes, $tblEventHashtags) {
                    return [
                        'id' => $tblEvent->id,
                        'startDate' => $tblEvent->event_start_date,
                        'endDate' => $tblEvent->event_end_date,
                        'startTime' => $tblEvent->start_time,
                        'endTime' => $tblEvent->end_time,
                        'type' => $mstEventTypes->where('id', $tblEvent->event_type_id)->first()->event_type_name,
                        'eventName' => $tblEvent->event_name,
                        'url' => $tblEvent->event_url,
                        'tag' => $tblEventHashtags->where('event_id', $tblEvent->id)->pluck('hashtag')->implode(','),
                    ];
                })->values(),
            ],
        ];
        return response()->json($responseData);
    }

    private function filterEventsByEventHashtags($tblEvents, $tblEventHashtags): Collection
    {
        return $tblEvents->filter(function ($tblEvent) use ($tblEventHashtags) {
            return $tblEventHashtags->where('event_id', $tblEvent->id)->isNotEmpty();
        });
    }

    private function filterEventsByNotEndDateOver($tblEvents, $reserveDays): Collection
    {
        return $tblEvents->filter(function ($tblEvent) use ($reserveDays) {
            if(empty($tblEvent->event_end_date)) return true;
            $endDate = Carbon::parse($tblEvent->event_end_date)->addDays($reserveDays);
            return $endDate->isAfter(Carbon::now());
        });
    }
}
