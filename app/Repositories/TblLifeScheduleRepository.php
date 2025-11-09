<?php

namespace App\Repositories;

use App\Models\TblLifeSchedule;

class TblLifeScheduleRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        $query = TblLifeSchedule::where('id', $id);
        return $query->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all()
    {
        return TblLifeSchedule::orderBy('schedule_date')
            ->orderBy('start_date_time')
            ->get();
    }

    /**
     * 指定日以降のスケジュール取得
     * @param  $date
     */
    public function afterScheduleDate($date)
    {
        return TblLifeSchedule::where('schedule_date', '>=', $date)
            ->orderBy('schedule_date')
            ->orderBy('start_date_time')
            ->get();
    }

    /**
     * 指定年月のスケジュール取得
     * @param  $yearMonth  年月 (YYYY-MM形式)
     */
    public function selectByYearMonth($startDate, $endDate)
    {
        return TblLifeSchedule::where('schedule_date', '>=', $startDate)
            ->where('schedule_date', '<=', $endDate)
            ->orderBy('schedule_date')
            ->orderBy('start_date_time')
            ->get();
    }

    /**
     * ページネーション検索\
     * ※取得するページ番号はリクエストパラメータの「page=」で指定することで自動で取得される
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblLifeSchedule::paginate($perPage);
    }

    /**
     * 指定日以降のスケジュール取得（ページネーション付き）
     * @param  string $date     検索開始日（YYYY-MM-DD形式）
     * @param  int    $perPage  １ページ中に表示するアイテム数
     */
    public function paginateAfterDate(string $date, int $perPage)
    {
        return TblLifeSchedule::where('schedule_date', '>=', $date)
            ->orderBy('schedule_date')
            ->orderBy('start_date_time')
            ->paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $entity
     */
    public function insert($entity)
    {
        return TblLifeSchedule::create($entity);
    }

    /**
     * 更新（主キー抽出）
     * @param  $entity
     * @param  $id
     */
    public function updateByPk($id, $entity)
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
            'schedule_date' => $object->schedule_date,
            'start_time' => $object->start_time,
            'end_time' => $object->end_time,
            'schedule_type' => $object->schedule_type,
            'schedule_contents' => $object->schedule_contents,
            'remarks' => $object->remarks,
            'notification_request_flag' => $object->notification_request_flag,
            'notification_comp_flag' => $object->notification_comp_flag,
            'created_program_name' => $object->created_program_name,
            'updated_program_name' => $object->updated_program_name,
        ];
    }
}
?>
