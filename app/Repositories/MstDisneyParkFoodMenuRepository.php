<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkFoodMenuAggregateList;
use App\Models\MstDisneyParkFoodMenu;
use Illuminate\Support\Collection;

class MstDisneyParkFoodMenuRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return MstDisneyParkFoodMenu::where('id', $id)->first();
    }

    /**
     * 複数件取得（全件）
     */
    public function all(): DisneyParkFoodMenuAggregateList
    {
        $entities = MstDisneyParkFoodMenu::orderBy('id')->get();
        return $this->createAggregateList($entities);
    }

    /**
     * ショップID一覧で取得
     *
     * @param list<int> $disneyParkFoodShopIds
     */
    public function findByDisneyParkFoodShopIds(
        array $disneyParkFoodShopIds
    ): DisneyParkFoodMenuAggregateList {
        if ($disneyParkFoodShopIds === []) {
            return new DisneyParkFoodMenuAggregateList(new Collection());
        }

        $entities = MstDisneyParkFoodMenu::whereIn(
            'disney_park_food_shop_id',
            $disneyParkFoodShopIds
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
        return MstDisneyParkFoodMenu::paginate($perPage);
    }

    /**
     * 新規登録
     * @param  $object
     */
    public function insert($object)
    {
        return MstDisneyParkFoodMenu::create($this->generateEntityByAllColume($object));
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
            'disney_park_food_shop_id' => $object->disney_park_food_shop_id ?? null,
            'menu_name' => $object->menu_name ?? null,
            'category_type' => $object->category_type ?? null,
            'price_yen' => $object->price_yen ?? null,
            'note' => $object->note ?? null,
            'time_limit_note' => $object->time_limit_note ?? null,
            'thumb_url' => $object->thumb_url ?? null,
            'official_url' => $object->official_url ?? null,
            'publish_start_date' => $object->publish_start_date ?? null,
            'publish_end_date' => $object->publish_end_date ?? null,
            'pause_flag' => $object->pause_flag ?? 0,
            'created_datetime' => $object->created_datetime ?? null,
            'updated_datetime' => $object->updated_datetime ?? null,
            'created_program_name' => $object->created_program_name ?? null,
            'updated_program_name' => $object->updated_program_name ?? null,
        ];
    }

    private function createAggregateList(Collection $entities): DisneyParkFoodMenuAggregateList
    {
        $aggregateList = new DisneyParkFoodMenuAggregateList(new Collection());
        foreach ($entities as $entity) {
            $aggregateList->add(new DisneyParkFoodMenuAggregate($entity));
        }
        return $aggregateList;
    }
}
