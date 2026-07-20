<?php

namespace App\Repositories;

use App\Models\TblUserDisneyParkFavorite;
use Illuminate\Support\Collection;

class TblUserDisneyParkFavoriteRepository
{
    /**
     * 1件取得（主キー抽出）
     * @param  $id
     */
    public function findPk($id)
    {
        return TblUserDisneyParkFavorite::where('id', $id)->first();
    }

    /**
     * ユーザーIDで一覧取得
     * @param  int $userId
     */
    public function findByUserId(int $userId): Collection
    {
        return TblUserDisneyParkFavorite::where('user_id', $userId)
            ->orderBy('favorite_type')
            ->orderBy('id')
            ->get();
    }

    /**
     * ユーザー・種別・対象で1件取得
     * @param  int $userId
     * @param  int $favoriteType
     * @param  int $targetId
     */
    public function findByUserTypeTarget(int $userId, int $favoriteType, int $targetId)
    {
        return TblUserDisneyParkFavorite::where('user_id', $userId)
            ->where('favorite_type', $favoriteType)
            ->where('target_id', $targetId)
            ->first();
    }

    /**
     * 新規登録
     * @param  $entity
     */
    public function insert($entity)
    {
        return TblUserDisneyParkFavorite::create($entity);
    }

    /**
     * ユーザー・種別・対象で削除
     * @param  int $userId
     * @param  int $favoriteType
     * @param  int $targetId
     */
    public function deleteByUserTypeTarget(int $userId, int $favoriteType, int $targetId): bool
    {
        $model = $this->findByUserTypeTarget($userId, $favoriteType, $targetId);
        if ($model === null) {
            return false;
        }
        $model->delete();
        return true;
    }
}
