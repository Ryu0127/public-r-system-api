<?php

namespace App\Repositories;

use App\Models\MstEvent;

class MstEventRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = MstEvent::where('id', $id);
        return $query->find();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return MstEvent::get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return MstEvent::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstEvent::create($this->generateEntityByAllColume($object));
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
            'event_name' => $object->event_name,
            'event_start_date' => $object->event_start_date,
            'event_end_date' => $object->event_end_date,
            'start_time' => $object->start_time,
            'end_time' => $object->end_time,
            'location' => $object->location,
            'address' => $object->address,
            'latitude' => $object->latitude,
            'longitude' => $object->longitude,
            'station' => $object->station,
            'event_url' => $object->event_url,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
