<?php

namespace App\Repositories;

use App\Contexts\Domain\Collection\Aggregates\TalentGroupAggregateList;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use App\Models\MstTalentGroup;
use Illuminate\Support\Collection;

class MstTalentGroupRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id): TalentGroupAggregate
    {
        $entity = MstTalentGroup::where('id', $id)->first();
        return new TalentGroupAggregate($entity);
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): TalentGroupAggregateList
    {
        $entities = MstTalentGroup::get();
        return $this->createAggregateList($entities);
    }

    public function getByTalentIds(array $talentIds): TalentGroupAggregateList
    {
        $entities = MstTalentGroup::select('mst_talent_group.*')
            ->join('rel_talent_group_member', 'mst_talent_group.id', '=', 'rel_talent_group_member.talent_group_id')
            ->join('mst_talent', 'rel_talent_group_member.talent_id', '=', 'mst_talent.id')
            ->whereIn('mst_talent.id', $talentIds)
            ->distinct()
            ->get();
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
        return MstTalentGroup::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstTalentGroup::create($this->generateEntityByAllColume($object));
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
            'group_type_id' => $object->group_type_id,
            'group_name' => $object->group_name,
        ];
    }


    private function createAggregateList(Collection $entities): TalentGroupAggregateList
    {
        $aggregateList = new TalentGroupAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new TalentGroupAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
