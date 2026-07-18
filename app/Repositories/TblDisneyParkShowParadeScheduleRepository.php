<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\DisneyParkShowParadeScheduleAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkShowParadeScheduleAggregateList;
use App\Models\TblDisneyParkShowParadeSchedule;
use Illuminate\Support\Collection;

class TblDisneyParkShowParadeScheduleRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return TblDisneyParkShowParadeSchedule::where('id', $id)->first();
    }

    /**
     * 複数件取得（全件・開始時刻順）
     */
    public function all(): DisneyParkShowParadeScheduleAggregateList
    {
        $entities = TblDisneyParkShowParadeSchedule::orderBy('start_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ショー・パレードIDで取得
     */
    public function findByShowParadeId(int $showParadeId): DisneyParkShowParadeScheduleAggregateList
    {
        $entities = TblDisneyParkShowParadeSchedule::where('show_parade_id', $showParadeId)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ショー・パレードID一覧で取得
     *
     * @param list<int> $showParadeIds
     */
    public function findByShowParadeIds(array $showParadeIds): DisneyParkShowParadeScheduleAggregateList
    {
        if ($showParadeIds === []) {
            return new DisneyParkShowParadeScheduleAggregateList(new Collection());
        }

        $entities = TblDisneyParkShowParadeSchedule::whereIn('show_parade_id', $showParadeIds)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ページネーション検索
     * @param  $object  検索条件
     * @param  int    $perPage １ページ中に表示するアイテム数
     */
    public function paginate($object, int $perPage)
    {
        return TblDisneyParkShowParadeSchedule::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return TblDisneyParkShowParadeSchedule::create($this->generateEntityByAllColume($object));
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
            'show_parade_id' => $object->show_parade_id ?? null,
            'start_time' => $object->start_time ?? null,
            'note' => $object->note ?? null,
            'cancel_flag' => $object->cancel_flag ?? 0,
            'created_datetime' => $object->created_datetime ?? null,
            'updated_datetime' => $object->updated_datetime ?? null,
            'created_program_name' => $object->created_program_name ?? null,
            'updated_program_name' => $object->updated_program_name ?? null,
        ];
    }

    private function createAggregateList(Collection $entities): DisneyParkShowParadeScheduleAggregateList
    {
        $aggregateList = new DisneyParkShowParadeScheduleAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new DisneyParkShowParadeScheduleAggregate($entity));
        }
        return $aggregateList;
    }
}
