<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\DisneyParkAttractionAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkAttractionAggregateList;
use App\Models\MstDisneyParkAttraction;
use Illuminate\Support\Collection;

class MstDisneyParkAttractionRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return MstDisneyParkAttraction::where('id', $id)->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): DisneyParkAttractionAggregateList
    {
        $entities = MstDisneyParkAttraction::orderBy('id')->get();
        return $this->createAggregateList($entities);
    }

    /**
     * パーク種別で取得
     */
    public function findByParkType(int $parkType): DisneyParkAttractionAggregateList
    {
        $entities = MstDisneyParkAttraction::where('park_type', $parkType)
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
        return MstDisneyParkAttraction::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstDisneyParkAttraction::create($this->generateEntityByAllColume($object));
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
            'park_type' => $object->park_type ?? null,
            'attraction_name' => $object->attraction_name ?? null,
            'area_type' => $object->area_type ?? null,
            'rank_type' => $object->rank_type ?? null,
            'duration_minutes' => $object->duration_minutes ?? null,
            'thumb_url' => $object->thumb_url ?? null,
            'publish_start_date' => $object->publish_start_date ?? null,
            'publish_end_date' => $object->publish_end_date ?? null,
            'dpa_flag' => $object->dpa_flag ?? 0,
            'priority_pass_flag' => $object->priority_pass_flag ?? 0,
            'pause_flag' => $object->pause_flag ?? 0,
            'created_datetime' => $object->created_datetime ?? null,
            'updated_datetime' => $object->updated_datetime ?? null,
            'created_program_name' => $object->created_program_name ?? null,
            'updated_program_name' => $object->updated_program_name ?? null,
        ];
    }

    private function createAggregateList(Collection $entities): DisneyParkAttractionAggregateList
    {
        $aggregateList = new DisneyParkAttractionAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new DisneyParkAttractionAggregate($entity));
        }
        return $aggregateList;
    }
}
