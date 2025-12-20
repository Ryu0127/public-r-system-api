<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\TalentSearchWordAggregateList;
use App\Contexts\Domain\Aggregates\TalentSearchWordAggregate;
use App\Models\MstTalentSearchWord;
use Illuminate\Support\Collection;

class MstTalentSearchWordRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id): TalentSearchWordAggregate
    {
        $entity = MstTalentSearchWord::where('id', $id)->first();
        return new TalentSearchWordAggregate($entity);
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): TalentSearchWordAggregateList
    {
        $entities = MstTalentSearchWord::get();
        return $this->createAggregateList($entities);
    }

    /**
     * タレントIDで取得
     * @param string $talentId
     */
    public function getByTalentId(string $talentId): TalentSearchWordAggregateList
    {
        $entities = MstTalentSearchWord::where('talent_id', $talentId)->get();
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
        return MstTalentSearchWord::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstTalentSearchWord::create($this->generateEntityByAllColume($object));
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
     * タレントIDで削除
     * @param string $talentId
     */
    public function deleteByTalentId(string $talentId)
    {
        return MstTalentSearchWord::where('talent_id', $talentId)->delete();
    }

    /**
     * Entityの生成（保存可能全カラム）
     * @param  $object
     */
    private function generateEntityByAllColume($object)
    {
        return [
            'id' => $object->id,
            'talent_id' => $object->talent_id,
            'search_word_group_id' => $object->search_word_group_id,
            'search_word' => $object->search_word,
            'created_datetime' => $object->created_datetime,
            'created_program_name' => $object->created_program_name,
            'updated_datetime' => $object->updated_datetime,
            'updated_program_name' => $object->updated_program_name,
        ];
    }

    private function createAggregateList(Collection $entities): TalentSearchWordAggregateList
    {
        $aggregateList = new TalentSearchWordAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new TalentSearchWordAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
