<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\DisneyParkAttractionWaitForecastAggregate;
use Illuminate\Support\Collection;

class DisneyParkAttractionWaitForecastAggregateList
{
    private $aggregates; // Collection

    public function __construct(Collection $aggregates)
    {
        $this->aggregates = $aggregates;
    }

    public function getAggregates(): Collection
    {
        return $this->aggregates;
    }

    public function getIds(): array
    {
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->id;
        })->toArray();
    }

    public function firstById($id): ?DisneyParkAttractionWaitForecastAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    /**
     * 対象営業日で絞る
     */
    public function filterByTargetDate(string $targetDate): DisneyParkAttractionWaitForecastAggregateList
    {
        return new DisneyParkAttractionWaitForecastAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($targetDate) {
                $date = $aggregate->getEntity()->target_date;
                if ($date instanceof \DateTimeInterface) {
                    return $date->format('Y-m-d') === $targetDate;
                }
                return (string) $date === $targetDate;
            })->values()
        );
    }

    /**
     * アトラクションID一覧で絞る
     *
     * @param list<int> $disneyParkAttractionIds
     */
    public function filterByDisneyParkAttractionIds(
        array $disneyParkAttractionIds
    ): DisneyParkAttractionWaitForecastAggregateList {
        $idSet = array_map('intval', $disneyParkAttractionIds);

        return new DisneyParkAttractionWaitForecastAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($idSet) {
                return in_array(
                    (int) $aggregate->getEntity()->disney_park_attraction_id,
                    $idSet,
                    true
                );
            })->values()
        );
    }

    /**
     * スロット時刻一覧（昇順・重複除去）を "H:i" 形式で返す
     *
     * @return list<string>
     */
    public function getSlotTimes(): array
    {
        return $this->aggregates
            ->map(function ($aggregate) {
                return self::formatSlotTime($aggregate->getEntity()->slot_time);
            })
            ->filter(function ($slotTime) {
                return $slotTime !== null;
            })
            ->unique()
            ->sort(function ($a, $b) {
                return self::slotToMinutes($a) <=> self::slotToMinutes($b);
            })
            ->values()
            ->toArray();
    }

    /**
     * アトラクションIDごとに wait 配列を組み立てる
     *
     * @param list<string> $slots
     * @return array<int, list<int|null>>
     */
    public function groupWaitMinutesByAttractionId(array $slots): array
    {
        $slotIndexByTime = [];
        foreach ($slots as $index => $slot) {
            $slotIndexByTime[$slot] = $index;
        }

        $grouped = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            $attractionId = (int) $entity->disney_park_attraction_id;
            $slotTime = self::formatSlotTime($entity->slot_time);
            if ($slotTime === null || !array_key_exists($slotTime, $slotIndexByTime)) {
                continue;
            }

            if (!isset($grouped[$attractionId])) {
                $grouped[$attractionId] = array_fill(0, count($slots), null);
            }

            $grouped[$attractionId][$slotIndexByTime[$slotTime]] =
                $entity->wait_minutes !== null ? (int) $entity->wait_minutes : null;
        }

        return $grouped;
    }

    public function add(DisneyParkAttractionWaitForecastAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }

    /**
     * @return string|null "H:i" （先頭ゼロなし）
     */
    public static function formatSlotTime($slotTime): ?string
    {
        if ($slotTime === null || $slotTime === '') {
            return null;
        }

        if (
            is_string($slotTime)
            && preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $slotTime, $matches)
        ) {
            return ((int) $matches[1]) . ':' . $matches[2];
        }

        if ($slotTime instanceof \DateTimeInterface) {
            return ((int) $slotTime->format('G')) . ':' . $slotTime->format('i');
        }

        return null;
    }

    private static function slotToMinutes(string $slot): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $slot, $matches)) {
            return 0;
        }
        return ((int) $matches[1]) * 60 + (int) $matches[2];
    }
}
