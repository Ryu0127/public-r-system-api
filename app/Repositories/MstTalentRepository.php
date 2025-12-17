<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\TalentAggregateList;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Models\MstTalent;
use Illuminate\Support\Collection;

class MstTalentRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id): TalentAggregate
    {
        $entity = MstTalent::where('id', $id)->first();
        return new TalentAggregate($entity);
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): TalentAggregateList
    {
        $entities = MstTalent::get();
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
        return MstTalent::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstTalent::create($this->generateEntityByAllColume($object));
    }

    /**
     * 更新（主キー抽出）
     * @param  $object
     * @param  $id
     */
    public function updateByPk($object, $id)
    {
        $model = $this->findPk($id)->getEntity();
        $model->update($this->generateEntityByAllColume($object));
        return $model;
    }

    /**
     * 削除（主キー抽出）
     * @param  $id
     */
    public function deleteByPk($id)
    {
        $model = $this->findPk($id)->getEntity();
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
            'talent_name' => $object->talent_name,
            'talent_name_en' => $object->talent_name_en,
            'group_name' => $object->group_name ?? null,
            'group_id' => $object->group_id ?? null,
            'twitter_accounts' => $object->twitter_accounts ?? null,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }


    private function createAggregateList(Collection $entities): TalentAggregateList
    {
        $aggregateList = new TalentAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new TalentAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
