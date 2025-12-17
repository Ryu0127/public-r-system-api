<?php

namespace App\Apis\OshiKatsuSaport;

use App\Contexts\Application\Services\Talent\EventHashtagApplicationService;
use App\Contexts\Application\Services\Talent\TalentHashtagApplicationService;
use App\Contexts\Domain\Aggregates\EventAggregate;
use App\Contexts\Domain\Aggregates\EventTypeAggregate;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentHashtagAggregate;
use App\Http\Controllers\Controller;
use App\Models\MstEventType;
use Illuminate\Http\JsonResponse;

class OshiKatsuSaportController extends Controller
{
    private $talentHashtagApplicationService;
    private $eventHashtagApplicationService;

    public function __construct(
        TalentHashtagApplicationService $talentHashtagApplicationService,
        EventHashtagApplicationService $eventHashtagApplicationService,
    ) {
        $this->talentHashtagApplicationService = $talentHashtagApplicationService;
        $this->eventHashtagApplicationService = $eventHashtagApplicationService;
    }

    /**
     * タレント一覧取得API
     * GET /oshi-katsu-saport/talents
     *
     * @return JsonResponse
     */
    public function talents(): JsonResponse
    {
        // select
        $talentAggregateList = $this->talentHashtagApplicationService->selectTalent();
        $talentHashtagAggregateList = $this->talentHashtagApplicationService->selectTalentHashtag();
        // find
        $foundTalentAggregateList = $this->talentHashtagApplicationService->findTalentByTalentHashtag($talentAggregateList, $talentHashtagAggregateList);
        // response
        $talentAggregates = $foundTalentAggregateList->getAggregates();
        $responseData = [
            'status' => true,
            'data' => [
                'talents' => $talentAggregates->map(function (TalentAggregate $talentAggregate) {
                    return [
                        'id' => $talentAggregate->getEntity()->id,
                        'talentName' => $talentAggregate->getEntity()->talent_name,
                        'talentNameEn' => $talentAggregate->getEntity()->talent_name_en,
                    ];
                }),
            ],
        ];
        return response()->json($responseData);
    }

    /**
     * エゴサーチサポート用タレント一覧取得API
     * GET /oshi-katsu-saport/ego-search/talents
     *
     * @return JsonResponse
     */
    public function egoSearchTalents(): JsonResponse
    {
        // select
        $talentAggregateList = $this->talentHashtagApplicationService->selectTalent();
        // response
        $talentAggregates = $talentAggregateList->getAggregates();
        $responseData = [
            'status' => true,
            'data' => [
                'talents' => $talentAggregates->map(function (TalentAggregate $talentAggregate) {
                    $entity = $talentAggregate->getEntity();
                    return [
                        'id' => $entity->id,
                        'talentName' => $entity->talent_name,
                        'talentNameEn' => $entity->talent_name_en,
                        'groupName' => '',
                        'groupId' => 0,
                        'twitterAccounts' => ["tokino_sora"],
                        // 将来的にプリセットデータなどを追加可能
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
        // select
        $talentAggregate = $this->talentHashtagApplicationService->findTalent($id);
        $talentHashtagAggregateList = $this->talentHashtagApplicationService->selectTalentHashtagByTalentId($id);
        $eventTypeAggregateList = $this->eventHashtagApplicationService->selectEventType();
        $eventCastTalentAggregateList = $this->eventHashtagApplicationService->selectEventCastTalentByTalentId($id);
        $eventAggregateList = $this->eventHashtagApplicationService->selectEventByIds($eventCastTalentAggregateList->getEventIds());
        $eventHashtagAggregateList = $this->eventHashtagApplicationService->selectEventHashtagByEventIds($eventAggregateList->getIds());
        // find
        $foundEventAggregateList = $this->eventHashtagApplicationService->findEventAggregateListByEventHashtags($eventAggregateList, $eventHashtagAggregateList);
        $foundEventAggregateList = $this->eventHashtagApplicationService->findEventAggregateListByNotEndDateOver($foundEventAggregateList, 7);
        // response
        $talentHashtagAggregates = $talentHashtagAggregateList->getAggregates();
        $eventAggregates = $foundEventAggregateList->getAggregates();
        $responseData = [
            'status' => true,
            'data' => [
                'talent' => [
                    'id' => $talentAggregate->getEntity()->id,
                    'name' => $talentAggregate->getEntity()->talent_name,
                ],
                'hashtags' => $talentHashtagAggregates->map(function (TalentHashtagAggregate $talentHashtagAggregate) {
                    return [
                        'id' => $talentHashtagAggregate->getEntity()->id,
                        'tag' => $talentHashtagAggregate->getEntity()->hashtag,
                        'description' => $talentHashtagAggregate->getEntity()->description,
                    ];
                })->values(),
                'eventHashtags' => $eventAggregates->map(function (EventAggregate $eventAggregate) use ($eventTypeAggregateList, $eventHashtagAggregateList) {
                    $eventTypeAggregate = $eventTypeAggregateList->firstById($eventAggregate->getEntity()->event_type_id) ?? new EventTypeAggregate(new MstEventType());
                    $eventHashtagAggregateList = $eventHashtagAggregateList->fillterByEventId($eventAggregate->getEntity()->id);
                    return [
                        'id' => $eventAggregate->getEntity()->id,
                        'startDate' => $eventAggregate->getEntity()->event_start_date,
                        'endDate' => $eventAggregate->getEntity()->event_end_date,
                        'startTime' => $eventAggregate->getEntity()->start_time,
                        'endTime' => $eventAggregate->getEntity()->end_time,
                        'type' => $eventTypeAggregate->getEntity()->event_type_name,
                        'eventName' => $eventAggregate->getEntity()->event_name,
                        'url' => $eventAggregate->getEntity()->event_url,
                        'tag' => implode(',', $eventHashtagAggregateList->getHashtags()),
                    ];
                })->values(),
            ],
        ];
        return response()->json($responseData);
    }
}
