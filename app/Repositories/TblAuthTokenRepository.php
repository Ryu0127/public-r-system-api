<?php

namespace App\Repositories;

use App\Models\TblAuthToken;

class TblAuthTokenRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $auth_token
     */
    public function findPk($auth_token)
    {
        $query = TblAuthToken::where('auth_token', $auth_token);
        return $query->find();
    }

    /**
     * 1件取得（トークンで検索）
     * @param  string $token
     * @return TblAuthToken|null
     */
    public function findByToken($token)
    {
        return TblAuthToken::where('auth_token', $token)->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return TblAuthToken::get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblAuthToken::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $entity
     */
    public function insert($entity)
    {
        return TblAuthToken::create($entity);
    }

    /**
     * 更新（主キー抽出）
     * @param  $object
     * @param  $auth_token
     */
    public function updateByPk($object, $auth_token)
    {
        $model = $this->findPk($auth_token);
        $model->update($this->generateEntityByAllColume($object));
        return $model;
    }

    /**
     * 削除（主キー抽出）
     * @param  $auth_token
     */
    public function deleteByPk($auth_token)
    {
        $model = $this->findPk($auth_token);
        $model->delete();
        return $model;
    }

    /**
     * Entityの生成（保存可能全カラム）
     * @param  $object
     */
    public function generateEntityByAllColume($object)
    {
        return [
            'auth_token' => $object->auth_token,
            'user_id' => $object->user_id,
            'expiration_date' => $object->expiration_date,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
