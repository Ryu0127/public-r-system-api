<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\EventAggregateList;
use App\Contexts\Domain\Aggregates\EventAggregate;
use App\Models\TblEvent;
use Illuminate\Support\Collection;

class TblEventRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = TblEvent::where('id', $id);
        return $query->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): EventAggregateList
    {
        $entities = TblEvent::get();
        return $this->createAggregateList($entities);
    }

    public function getByPkIds(array $ids): EventAggregateList
    {
        $entities = TblEvent::whereIn('id', $ids)->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblEvent::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return TblEvent::create($this->generateEntityByAllColume($object));
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
        $entity = [
            'event_name' => $object->event_name ?? null,
            'event_start_date' => $object->event_start_date ?? null,
            'event_end_date' => $object->event_end_date ?? null,
            'start_time' => $object->start_time ?? null,
            'end_time' => $object->end_time ?? null,
            'location' => $object->location ?? null,
            'address' => $object->address ?? null,
            'latitude' => $object->latitude ?? null,
            'longitude' => $object->longitude ?? null,
            'station' => $object->station ?? null,
            'event_url' => $object->event_url ?? null,
            'event_type_id' => $object->event_type_id ?? null,
            'description' => $object->description ?? null,
            'note' => $object->note ?? null,
            'created_datetime' => $object->created_datetime ?? now(),
            'updated_datetime' => $object->updated_datetime ?? now(),
        ];

        // IDが設定されている場合のみ追加
        if (isset($object->id)) {
            $entity['id'] = $object->id;
        }

        return $entity;
    }
    
    private function createAggregateList(Collection $entities): EventAggregateList
    {
        $aggregateList = new EventAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new EventAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
