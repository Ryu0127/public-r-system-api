<?php

namespace App\Repositories;

use App\Models\MstTalentHashtag;

class MstTalentHashtagRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = MstTalentHashtag::where('id', $id);
        return $query->find();
    }

    public function getByTalentId($talentId)
    {
        $query = MstTalentHashtag::where('talent_id', $talentId);
        return $query->get();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return MstTalentHashtag::get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return MstTalentHashtag::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstTalentHashtag::create($this->generateEntityByAllColume($object));
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
            'talent_id' => $object->talent_id,
            'hashtag_type_id' => $object->hashtag_type_id,
            'hashtag' => $object->hashtag,
            'description' => $object->description,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
