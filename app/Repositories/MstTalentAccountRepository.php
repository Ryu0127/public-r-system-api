<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\Collection\TalentAccountAggregateList;
use App\Contexts\Domain\Aggregates\TalentAccountAggregate;
use App\Models\MstTalentAccount;
use Illuminate\Support\Collection;

class MstTalentAccountRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk(string $id): TalentAccountAggregate
    {
        $entity = MstTalentAccount::where('id', $id)->first();
        return new TalentAccountAggregate($entity);
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): TalentAccountAggregateList
    {
        $entities = MstTalentAccount::get();
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
        return MstTalentAccount::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstTalentAccount::create($this->generateEntityByAllColume($object));
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
            'talent_id' => $object->talent_id,
            'account_type_id' => $object->account_type_id,
            'account_code' => $object->account_code,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }

    private function createAggregateList(Collection $entities): TalentAccountAggregateList
    {
        $aggregateList = new TalentAccountAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new TalentAccountAggregate($entity));
        }
        return $aggregateList;
    }
}
?>
