<?php

namespace App\Repositories;

use App\Models\TblLifeScheduleNotification;

class TblLifeScheduleNotificationRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = TblLifeScheduleNotification::where('id', $id);
        return $query->find();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return TblLifeScheduleNotification::get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblLifeScheduleNotification::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $entity
     */
    public function insert($entity)
    {
        return TblLifeScheduleNotification::create($entity);
    }

    /**
     * 更新（主キー抽出）
     * @param  $entity
     * @param  $id
     */
    public function updateByPk($entity, $id)
    {
        $model = $this->findPk($id);
        $model->update($entity);
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
            'life_schedule_id' => $object->life_schedule_id,
            'notification_date_time' => $object->notification_date_time,
            'notification_comp_flag' => $object->notification_comp_flag,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
