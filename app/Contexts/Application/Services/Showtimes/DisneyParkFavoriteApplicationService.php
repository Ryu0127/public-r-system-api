<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Repositories\MstDisneyParkAttractionRepository;
use App\Repositories\MstDisneyParkFoodMenuRepository;
use App\Repositories\MstDisneyParkShowParadeRepository;
use App\Repositories\TblUserDisneyParkFavoriteRepository;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class DisneyParkFavoriteApplicationService
{
    /** favorite_type: ショーパレ */
    public const FAVORITE_TYPE_SHOW_PARADE = 1;
    /** favorite_type: アトラクション */
    public const FAVORITE_TYPE_ATTRACTION = 2;
    /** favorite_type: フードメニュー */
    public const FAVORITE_TYPE_FOOD_MENU = 3;

    private const PROGRAM_NAME = 'showtimes-favorites-api';

    private TblUserDisneyParkFavoriteRepository $tblUserDisneyParkFavoriteRepository;
    private MstDisneyParkShowParadeRepository $mstDisneyParkShowParadeRepository;
    private MstDisneyParkAttractionRepository $mstDisneyParkAttractionRepository;
    private MstDisneyParkFoodMenuRepository $mstDisneyParkFoodMenuRepository;

    public function __construct(
        TblUserDisneyParkFavoriteRepository $tblUserDisneyParkFavoriteRepository,
        MstDisneyParkShowParadeRepository $mstDisneyParkShowParadeRepository,
        MstDisneyParkAttractionRepository $mstDisneyParkAttractionRepository,
        MstDisneyParkFoodMenuRepository $mstDisneyParkFoodMenuRepository
    ) {
        $this->tblUserDisneyParkFavoriteRepository = $tblUserDisneyParkFavoriteRepository;
        $this->mstDisneyParkShowParadeRepository = $mstDisneyParkShowParadeRepository;
        $this->mstDisneyParkAttractionRepository = $mstDisneyParkAttractionRepository;
        $this->mstDisneyParkFoodMenuRepository = $mstDisneyParkFoodMenuRepository;
    }

    /**
     * ユーザーのお気に入り一覧を種別ごとの ID 配列で返す
     * @return array{showParadeIds: list<int>, attractionIds: list<int>, foodMenuIds: list<int>}
     */
    public function listByUserId(int $userId): array
    {
        $favorites = $this->tblUserDisneyParkFavoriteRepository->findByUserId($userId);

        return [
            'showParadeIds' => $this->pluckTargetIds($favorites, self::FAVORITE_TYPE_SHOW_PARADE),
            'attractionIds' => $this->pluckTargetIds($favorites, self::FAVORITE_TYPE_ATTRACTION),
            'foodMenuIds' => $this->pluckTargetIds($favorites, self::FAVORITE_TYPE_FOOD_MENU),
        ];
    }

    /**
     * お気に入り登録
     * @throws InvalidArgumentException
     */
    public function add(int $userId, int $favoriteType, int $targetId): void
    {
        $this->assertValidFavoriteType($favoriteType);
        $this->assertTargetExists($favoriteType, $targetId);

        $existing = $this->tblUserDisneyParkFavoriteRepository->findByUserTypeTarget(
            $userId,
            $favoriteType,
            $targetId
        );
        if ($existing !== null) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $this->tblUserDisneyParkFavoriteRepository->insert([
            'user_id' => $userId,
            'favorite_type' => $favoriteType,
            'target_id' => $targetId,
            'created_datetime' => $now,
            'updated_datetime' => $now,
            'created_program_name' => self::PROGRAM_NAME,
            'updated_program_name' => self::PROGRAM_NAME,
        ]);
    }

    /**
     * お気に入り解除
     * @return bool 削除できた場合 true（既に無い場合 false）
     * @throws InvalidArgumentException
     */
    public function remove(int $userId, int $favoriteType, int $targetId): bool
    {
        $this->assertValidFavoriteType($favoriteType);

        return $this->tblUserDisneyParkFavoriteRepository->deleteByUserTypeTarget(
            $userId,
            $favoriteType,
            $targetId
        );
    }

    /**
     * @param Collection $favorites
     * @return list<int>
     */
    private function pluckTargetIds(Collection $favorites, int $favoriteType): array
    {
        return $favorites
            ->where('favorite_type', $favoriteType)
            ->pluck('target_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function assertValidFavoriteType(int $favoriteType): void
    {
        $valid = [
            self::FAVORITE_TYPE_SHOW_PARADE,
            self::FAVORITE_TYPE_ATTRACTION,
            self::FAVORITE_TYPE_FOOD_MENU,
        ];
        if (!in_array($favoriteType, $valid, true)) {
            throw new InvalidArgumentException('favoriteType が不正です');
        }
    }

    private function assertTargetExists(int $favoriteType, int $targetId): void
    {
        $exists = false;
        if ($favoriteType === self::FAVORITE_TYPE_SHOW_PARADE) {
            $exists = $this->mstDisneyParkShowParadeRepository->findPk($targetId) !== null;
        } elseif ($favoriteType === self::FAVORITE_TYPE_ATTRACTION) {
            $exists = $this->mstDisneyParkAttractionRepository->findPk($targetId) !== null;
        } elseif ($favoriteType === self::FAVORITE_TYPE_FOOD_MENU) {
            $exists = $this->mstDisneyParkFoodMenuRepository->findPk($targetId) !== null;
        }

        if (!$exists) {
            throw new InvalidArgumentException('お気に入り対象が存在しません');
        }
    }
}
