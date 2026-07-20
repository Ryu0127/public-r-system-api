<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuContentAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkFoodMenuContentAggregateList;
use App\Models\MstDisneyParkFoodMenuContent;
use Illuminate\Support\Collection;

class MstDisneyParkFoodMenuContentRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return MstDisneyParkFoodMenuContent::where('id', $id)->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): DisneyParkFoodMenuContentAggregateList
    {
        $entities = MstDisneyParkFoodMenuContent::orderBy('id')->get();
        return $this->createAggregateList($entities);
    }

    /**
     * メニューID一覧で取得
     *
     * @param list<int> $disneyParkFoodMenuIds
     */
    public function findByDisneyParkFoodMenuIds(
        array $disneyParkFoodMenuIds
    ): DisneyParkFoodMenuContentAggregateList {
        if ($disneyParkFoodMenuIds === []) {
            return new DisneyParkFoodMenuContentAggregateList(new Collection());
        }

        $entities = MstDisneyParkFoodMenuContent::whereIn(
            'disney_park_food_menu_id',
            $disneyParkFoodMenuIds
        )
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
        return MstDisneyParkFoodMenuContent::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstDisneyParkFoodMenuContent::create($this->generateEntityByAllColume($object));
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
            'disney_park_food_menu_id' => $object->disney_park_food_menu_id ?? null,
            'content_name' => $object->content_name ?? null,
            'pause_flag' => $object->pause_flag ?? 0,
            'created_datetime' => $object->created_datetime ?? null,
            'updated_datetime' => $object->updated_datetime ?? null,
            'created_program_name' => $object->created_program_name ?? null,
            'updated_program_name' => $object->updated_program_name ?? null,
        ];
    }

    private function createAggregateList(Collection $entities): DisneyParkFoodMenuContentAggregateList
    {
        $aggregateList = new DisneyParkFoodMenuContentAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new DisneyParkFoodMenuContentAggregate($entity));
        }
        return $aggregateList;
    }
}
