<?php

namespace App\Repositories;

use App\Models\MstUser;

class MstUserRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = MstUser::where('id', $id);
        return $query->first();
    }

    /**
     * メールアドレスで検索
     * @param  $mailAddress
     */
    public function findByMailAddress($mailAddress)
    {
        return MstUser::where('mail_address', $mailAddress)
            ->where('active_flag', 1)
            ->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return MstUser::get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return MstUser::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstUser::create($this->generateEntityByAllColume($object));
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
            'mail_address' => $object->mail_address,
            'password' => $object->password,
            'active_flag' => $object->active_flag,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
