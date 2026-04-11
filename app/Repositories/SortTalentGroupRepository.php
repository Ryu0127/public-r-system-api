<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\SortTalentGroupAggregate;
use App\Contexts\Domain\Collection\Aggregates\SortTalentGroupAggregateList;
use App\Models\SortTalentGroup;
use Illuminate\Support\Collection;

class SortTalentGroupRepository
{
    /**
     * 複数件取得（全件）
     */
    public function all(): SortTalentGroupAggregateList
    {
        return $this->createAggregateList(SortTalentGroup::get());
    }

    /**
     * 複数件取得（ソート種別抽出）
     * @param  $sortType
     */
    public function getBySortType($sortType): SortTalentGroupAggregateList
    {
        $query = SortTalentGroup::where('sort_type', $sortType)
            ->orderBy('sort_no', 'asc')
            ->get();
        return $this->createAggregateList($query);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return SortTalentGroup::create($this->generateEntityByAllColume($object));
    }

    /**
     * 削除（主キー抽出）
     * @param  $id
     */
    public function deleteByPk($id)
    {
        $entity = SortTalentGroup::find($id);
        $entity->delete();
    }

    /**
     * Entityの生成（保存可能全カラム）
     * @param  $object
     */
    private function generateEntityByAllColume($object)
    {
        return [
            'id' => $object->id,
            'youtube_music_video_id' => $object->youtube_music_video_id,
            'talent_id' => $object->talent_id,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }

    private function createAggregateList(Collection $entities): SortTalentGroupAggregateList
    {
        return new SortTalentGroupAggregateList(new Collection($entities->map(function($entity){
            return new SortTalentGroupAggregate($entity); // SortTalentGroupAggregate
        })));
    }
}
?>
