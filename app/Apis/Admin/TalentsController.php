<?php

namespace App\Apis\Admin;

use App\Contexts\Application\Services\Talent\TalentAdminApplicationService;
use App\Contexts\Domain\Aggregates\TalentAccountAggregate;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Http\Controllers\Controller;
use App\Models\MstTalent;
use App\Models\MstTalentAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TalentsController extends Controller
{
    private $talentAdminApplicationService;

    public function __construct(
        TalentAdminApplicationService $talentAdminApplicationService,
    ) {
        $this->talentAdminApplicationService = $talentAdminApplicationService;
    }

    /**
     * タレント一覧取得API
     * GET /admin/talents
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // select
        $talentAggregateList = $this->talentAdminApplicationService->selectTalent();
        $talentAccountAggregateList = $this->talentAdminApplicationService->selectTalentAccount();
        $talentHashtagAggregateList = $this->talentAdminApplicationService->selectTalentHashtag();

        // response
        $responseData = [
            'success' => true,
            'data' => $talentAggregateList->getAggregates()->map(function ($talentAggregate) use ($talentAccountAggregateList, $talentHashtagAggregateList) {
                $entity = $talentAggregate->getEntity();

                // タレントIDに紐づくアカウント情報を取得
                $filteredAccountList = $talentAccountAggregateList->getAggregates()->filter(function ($accountAggregate) use ($entity) {
                    return $accountAggregate->getEntity()->talent_id === $entity->id;
                });

                // タレントIDに紐づくハッシュタグ情報を取得
                $filteredHashtagList = $talentHashtagAggregateList->getAggregates()->filter(function ($hashtagAggregate) use ($entity) {
                    return $hashtagAggregate->getEntity()->talent_id === $entity->id;
                });

                // Twitterアカウントのみを抽出
                $twitterAccounts = $filteredAccountList
                    ->filter(function ($accountAggregate) {
                        return $accountAggregate->getEntity()->account_type === 'twitter';
                    })
                    ->map(function ($accountAggregate) {
                        return $accountAggregate->getEntity()->account_code;
                    })
                    ->values()
                    ->toArray();

                return [
                    'id' => $entity->id,
                    'talentName' => $entity->talent_name,
                    'talentNameEn' => $entity->talent_name_en,
                    'groupName' => $entity->group_name,
                    'groupId' => $entity->group_id,
                    'twitterAccounts' => $twitterAccounts,
                    'accounts' => $filteredAccountList->map(function ($accountAggregate) {
                        $account = $accountAggregate->getEntity();
                        return [
                            'id' => $account->id,
                            'accountType' => $account->account_type,
                            'accountCode' => $account->account_code,
                        ];
                    })->values(),
                    'hashtags' => $filteredHashtagList->map(function ($hashtagAggregate) {
                        $hashtag = $hashtagAggregate->getEntity();
                        return [
                            'id' => $hashtag->id,
                            'hashtag' => $hashtag->hashtag,
                            'description' => $hashtag->description,
                        ];
                    })->values(),
                    'createdProgramName' => $entity->created_program_name,
                    'updatedProgramName' => $entity->updated_program_name,
                ];
            })->values(),
            'message' => 'タレント一覧を取得しました',
        ];

        return response()->json($responseData);
    }

    /**
     * タレント詳細取得API
     * GET /admin/talents/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            // select
            $talentAggregate = $this->talentAdminApplicationService->findTalentById((int)$id);
            $talentAccountAggregateList = $this->talentAdminApplicationService->selectTalentAccount();
            $talentHashtagAggregateList = $this->talentAdminApplicationService->selectTalentHashtag();

            $entity = $talentAggregate->getEntity();

            // タレントIDに紐づくアカウント情報を取得
            $filteredAccountList = $talentAccountAggregateList->getAggregates()->filter(function ($accountAggregate) use ($entity) {
                return $accountAggregate->getEntity()->talent_id === $entity->id;
            });

            // タレントIDに紐づくハッシュタグ情報を取得
            $filteredHashtagList = $talentHashtagAggregateList->getAggregates()->filter(function ($hashtagAggregate) use ($entity) {
                return $hashtagAggregate->getEntity()->talent_id === $entity->id;
            });

            // Twitterアカウントのみを抽出
            $twitterAccounts = $filteredAccountList
                ->filter(function ($accountAggregate) {
                    return $accountAggregate->getEntity()->account_type_id == 1;
                })
                ->map(function ($accountAggregate) {
                    return $accountAggregate->getEntity()->account_code;
                })
                ->values()
                ->toArray();

            // response
            $responseData = [
                'success' => true,
                'data' => [
                    'id' => $entity->id,
                    'talentName' => $entity->talent_name,
                    'talentNameEn' => $entity->talent_name_en,
                    'groupName' => $entity->group_name,
                    'groupId' => $entity->group_id,
                    'twitterAccounts' => $twitterAccounts,
                    'accounts' => $filteredAccountList->map(function ($accountAggregate) {
                        $account = $accountAggregate->getEntity();
                        return [
                            'id' => $account->id,
                            'accountType' => $account->account_type,
                            'accountCode' => $account->account_code,
                        ];
                    })->values(),
                    'hashtags' => $filteredHashtagList->map(function ($hashtagAggregate) {
                        $hashtag = $hashtagAggregate->getEntity();
                        return [
                            'id' => $hashtag->id,
                            'hashtag' => $hashtag->hashtag,
                            'description' => $hashtag->description,
                        ];
                    })->values(),
                    'createdProgramName' => $entity->created_program_name,
                    'updatedProgramName' => $entity->updated_program_name,
                ],
                'message' => 'タレント詳細を取得しました',
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'タレントが見つかりませんでした',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * タレント登録API
     * POST /admin/talents
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // リクエストデータのバリデーション
        $validator = Validator::make($request->all(), [
            'talentName' => 'required|string|max:255',
            'talentNameEn' => 'nullable|string|max:255',
            'groupName' => 'nullable|string|max:255',
            'groupId' => 'nullable|integer',
            'twitterAccounts' => 'nullable|array',
            'twitterAccounts.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラーが発生しました',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // タレントデータをオブジェクトに変換
            $talentEntity = new MstTalent();
            $talentEntity->talent_name = $request->talentName;
            $talentEntity->talent_name_en = $request->talentNameEn;
            $talentEntity->group_name = $request->groupName;
            $talentEntity->group_id = $request->groupId;
            $talentEntity->twitter_accounts = $request->twitterAccounts;
            $talentEntity->created_program_name = 'admin-api';
            $talentEntity->updated_program_name = 'admin-api';

            $talentAggregate = new TalentAggregate($talentEntity);

            // タレントを登録
            $talentAggregate = $this->talentAdminApplicationService->insertTalent($talentAggregate);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'タレントを登録しました',
                'data' => [
                    'id' => $talentAggregate->getEntity()->id,
                    'talentName' => $talentAggregate->getEntity()->talent_name,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'タレントの登録に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * タレント更新API
     * PUT /admin/talents/{id}
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // リクエストデータのバリデーション
        // $validator = Validator::make($request->all(), [
        //     'talentName' => 'required|string|max:255',
        //     'talentNameEn' => 'nullable|string|max:255',
        //     'groupName' => 'nullable|string|max:255',
        //     'groupId' => 'nullable|integer',
        //     'twitterAccounts' => 'nullable|array',
        //     'twitterAccounts.*' => 'string|max:255',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'バリデーションエラーが発生しました',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }

        DB::beginTransaction();
        try {
            // 既存のタレントを取得して確認
            $existingTalentAggregate = $this->talentAdminApplicationService->findTalentById((int)$id);
            $existingTalentAccountAggregateList = $this->talentAdminApplicationService->selectTalentAccount();

            // タレントデータをオブジェクトに変換
            $talentEntity = new MstTalent();
            $talentEntity->id = (int)$id;
            $talentEntity->talent_name = $request->talentName;
            $talentEntity->talent_name_en = $request->talentNameEn;
            $talentEntity->updated_program_name = 'admin-api';
            $talentAggregate = new TalentAggregate($talentEntity);

            foreach ($request->twitterAccounts as $twitterAccount) {
                $talentAccountEntity = new MstTalentAccount();
                $talentAccountEntity->talent_id = (int)$id;
                $talentAccountEntity->account_type_id = 1;
                $talentAccountEntity->account_code = $twitterAccount;
                $talentAccountEntity->created_datetime = now();
                $talentAccountEntity->created_program_name = 'admin-api';
                $talentAccountEntity->updated_datetime = now();
                $talentAccountEntity->updated_program_name = 'admin-api';
                $talentAccountAggregate = new TalentAccountAggregate($talentAccountEntity);
                $existingTalentAccountAggregate = $existingTalentAccountAggregateList->findByAccountCode($twitterAccount);
                if(is_null($existingTalentAccountAggregate)) {
                    $talentAccountAggregate = $this->talentAdminApplicationService->insertTalentAccount($talentAccountAggregate);
                }
            }

            // タレントを更新
            $talentAggregate = $this->talentAdminApplicationService->updateTalent($talentAggregate, (int)$id);
            // タレントアカウントの登録更新

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'タレントを更新しました',
                'data' => [
                    'id' => $talentAggregate->getEntity()->id,
                    'talentName' => $talentAggregate->getEntity()->talent_name,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'タレントの更新に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * タレント削除API
     * DELETE /admin/talents/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            // タレントを削除
            $talentAggregate = $this->talentAdminApplicationService->deleteTalent((int)$id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'タレントを削除しました',
                'data' => [
                    'id' => $talentAggregate->getEntity()->id,
                    'talentName' => $talentAggregate->getEntity()->talent_name,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'タレントの削除に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
