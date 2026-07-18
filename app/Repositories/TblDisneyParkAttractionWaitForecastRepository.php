<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\DisneyParkAttractionWaitForecastAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkAttractionWaitForecastAggregateList;
use App\Models\TblDisneyParkAttractionWaitForecast;
use Illuminate\Support\Collection;

class TblDisneyParkAttractionWaitForecastRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return TblDisneyParkAttractionWaitForecast::where('id', $id)->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): DisneyParkAttractionWaitForecastAggregateList
    {
        $entities = TblDisneyParkAttractionWaitForecast::orderBy('slot_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * 対象営業日で取得
     */
    public function findByTargetDate(string $targetDate): DisneyParkAttractionWaitForecastAggregateList
    {
        $entities = TblDisneyParkAttractionWaitForecast::where('target_date', $targetDate)
            ->orderBy('slot_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * アトラクションID一覧と対象営業日で取得
     *
     * @param list<int> $disneyParkAttractionIds
     */
    public function findByDisneyParkAttractionIdsAndTargetDate(
        array $disneyParkAttractionIds,
        string $targetDate
    ): DisneyParkAttractionWaitForecastAggregateList {
        if ($disneyParkAttractionIds === []) {
            return new DisneyParkAttractionWaitForecastAggregateList(new Collection());
        }

        $entities = TblDisneyParkAttractionWaitForecast::where('target_date', $targetDate)
            ->whereIn('disney_park_attraction_id', $disneyParkAttractionIds)
            ->orderBy('slot_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ページネーション検索
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblDisneyParkAttractionWaitForecast::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return TblDisneyParkAttractionWaitForecast::create($this->generateEntityByAllColume($object));
    }

    /**
     * 更新（主キー抽出）
     * @param  $object
     * @param  $id
     */
    public function updateByPk($object, $id)
    {
        $model = $this->findPk($id);
        $model->update($this->generateEntityByAllColume($object));
        return $model;
    }

    /**
     * 削除（主キー抽出）
     * @param  $id
     */
    public function deleteByPk($id)
    {
        $model = $this->findPk($id);
        $model->delete();
        return $model;
    }

    /**
     * Entityの生成（保存可能全カラム）
     * @param  $object
     */
    private function generateEntityByAllColume($object)
    {
        return [
            'disney_park_attraction_id' => $object->disney_park_attraction_id ?? null,
            'target_date' => $object->target_date ?? null,
            'slot_time' => $object->slot_time ?? null,
            'wait_minutes' => $object->wait_minutes ?? null,
            'created_datetime' => $object->created_datetime ?? null,
            'updated_datetime' => $object->updated_datetime ?? null,
            'created_program_name' => $object->created_program_name ?? null,
            'updated_program_name' => $object->updated_program_name ?? null,
        ];
    }

    private function createAggregateList(Collection $entities): DisneyParkAttractionWaitForecastAggregateList
    {
        $aggregateList = new DisneyParkAttractionWaitForecastAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new DisneyParkAttractionWaitForecastAggregate($entity));
        }
        return $aggregateList;
    }
}
