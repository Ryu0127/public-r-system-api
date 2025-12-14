<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\EventHashtagAggregateList;
use App\Contexts\Domain\Aggregates\EventHashtagAggregate;
use App\Models\TblEventHashtag;
use Illuminate\Support\Collection;

class TblEventHashtagRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = TblEventHashtag::where('id', $id);
        return $query->find();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return TblEventHashtag::get();
    }

    public function getByEventIds(array $eventIds): EventHashtagAggregateList
    {
        $entities = TblEventHashtag::whereIn('event_id', $eventIds)->get();
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
        return TblEventHashtag::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return TblEventHashtag::create($this->generateEntityByAllColume($object));
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
            'hashtag' => $object->hashtag,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }

    private function createAggregateList(Collection $entities): EventHashtagAggregateList
    {
        $aggregates = new Collection();
        foreach ($entities as $entity) {
            $aggregates->push(new EventHashtagAggregate($entity));
        }
        return new EventHashtagAggregateList($aggregates);
    }
}
?>
