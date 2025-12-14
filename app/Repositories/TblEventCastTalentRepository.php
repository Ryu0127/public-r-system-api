<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\EventCastTalentAggregateList;
use App\Contexts\Domain\Aggregates\EventCastTalentAggregate;
use App\Models\TblEventCastTalent;
use Illuminate\Support\Collection;

class TblEventCastTalentRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = TblEventCastTalent::where('id', $id);
        return $query->find();
    }

    public function getByTalentId(string $talentId): EventCastTalentAggregateList
    {
        $entities = TblEventCastTalent::where('talent_id', $talentId)->get();
        return $this->createAggregateList($entities);
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): EventCastTalentAggregateList
    {
        $entities = TblEventCastTalent::get();
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
        return TblEventCastTalent::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return TblEventCastTalent::create($this->generateEntityByAllColume($object));
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
            'id' => $object->id,
            'event_id' => $object->event_id,
            'talent_id' => $object->talent_id,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
    
    private function createAggregateList(Collection $entities): EventCastTalentAggregateList
    {
        $aggregateList = new EventCastTalentAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new EventCastTalentAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
