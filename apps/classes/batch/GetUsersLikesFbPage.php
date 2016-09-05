<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.batch.GetFacebookPagePostInfoBatchBase');

class GetUserLikesFbPage extends GetFacebookPagePostInfoBatchBase {
    const LIKE_FLAG_LIKED = 1;
    const LIKE_FLAG_NOT_LIKED = 0;

    public function executeProcess()
    {
        //すべてベージを取得する
        $entries = $this->getAllEntries();
        $detailCrawlerUrlService = $this->service_factory->create('DetailCrawlerUrlService');

        try {
            $accessToken = $this->getFacebookAccessToken($this->facebookUserTest['userId']);
            if (!$accessToken) {
                throw new aafwException('GetUserLikesFbPage Get Facebook Access Token failed! ');
            }

            $this->facebookApiClient->setToken($accessToken);
        } catch (Exception $e) {
            $this->logger->error('GetUserLikesFbPage Set Facebook AccessToken Error!');
            $this->logger->error($e);
            return;
        }

        $this->request_count = 0;
        foreach ($entries as $entry) {
            $nextUrl = null;
            $count = 0;
            $sql = "";
            $crawlerUrl = $detailCrawlerUrlService->checkExistFbLike($entry['object_id'], $entry['type']);
            $previousUrl = ($crawlerUrl->url !== "") ? $crawlerUrl->url : "";

            if ($previousUrl == null) {
                while (1) {
                    $result = $this->getExistsLikesFacebookPagePost($entry['post_id'], $nextUrl);
                    if (!$result) break;

                    list($listLikedUsers, $nextUrl) = $result;

                    if (count($listLikedUsers) > 0) {
                        list($sql, $count) = $this->createSqlToUpdateUserLikes($listLikedUsers, $count, $sql, $entry['object_id']);
                    }

                    ///投稿の全て Likeを取得する場合は次のページへ
                    if (count($listLikedUsers) < self::FB_API_LIMIT_RECORD) {
                        if ($count > 0) {
                            $sql = substr($sql, 0, strlen($sql) - 1);
                            $sql .= " ON DUPLICATE KEY UPDATE like_flg = VALUES (like_flg),updated_at = NOW()";
                            $this->db->executeUpdate($sql);
                        }

                        list($listLikedUsers, $previousUrl) = $this->getNewLikesFacebookPagePost($entry['post_id'], $previousUrl);
                        break;
                    }
                }
            } else {
                $result = $this->getNewLikesFacebookPagePost($entry['post_id'], $previousUrl);
                if (!$result) continue;

                list($listLikedUsers, $previousUrl) = $this->getNewLikesFacebookPagePost($entry['post_id'], $previousUrl);
                $count = 0;
                $sql = "";
                if (count($listLikedUsers) > 0) {
                    list($sql, $count) = $this->createSqlToUpdateUserLikes($listLikedUsers, $count, $sql, $entry['object_id']);
                }

                if ($count > 0) {
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= " ON DUPLICATE KEY UPDATE like_flg = VALUES (like_flg),updated_at = NOW()";
                    $this->db->executeUpdate($sql);
                }
            }

            $this->updateDetailCrawlerUrl($entry['object_id'], $entry['type'], DetailCrawlerUrl::DATA_TYPE_LIKE, $previousUrl);
        }
    }

    /**
     * 以前の「いいね」しているユーザーを取得する
     * @param $postId
     * @param $nextUrl
     * @return array
     */
    public function getExistsLikesFacebookPagePost($postId, $nextUrl)
    {
        $likedUsers = array();
        $limit = self::FB_API_LIMIT_RECORD;

        if ($this->request_count >= self::FB_REQUEST_LIMIT) {
            $this->request_count = 0;
            sleep(600);
        }

        try {
            if ($nextUrl == null) {
                $request = "/{$postId}/likes?fields=id&limit={$limit}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    foreach ($response['data'] as $value) {
                        array_push($likedUsers, $value->id);
                    }
                    $nextUrl = $response['paging']->cursors->after;
                }
            } else {
                $request = "/{$postId}/likes?fields=id&limit={$limit}&after={$nextUrl}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    foreach ($response['data'] as $value) {
                        array_push($likedUsers, $value->id);
                    }
                    $nextUrl = $response['paging']->cursors->after;
                }
            }

            return array($likedUsers, $nextUrl);
        } catch (Exception $e) {
            $this->logger->error('GetUsersLikesFbPage getExistsLikesFacebookPagePost Error. $postId = ' . $postId . ' current request_count = ' . $this->request_count);
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * 「いいね」しているユーザー情報をデータベースに追加するSQLを作成する
     * @param $listLikedUsers
     * @param $count
     * @param $sql
     * @param $object_id
     * @return array
     */
    public function createSqlToUpdateUserLikes($listLikedUsers, $count, $sql, $object_id)
    {
        foreach ($listLikedUsers as $likedUser) {
            $count++;
            if ($count == 1) {
                $sql .= "INSERT INTO fb_entries_users_likes(fb_uid, object_id,like_flg,created_at, updated_at) VALUES";
            }
            $userLike = array(
                'fb_uid' => (integer)$likedUser,
                'object_id' => (integer)$object_id,
                'like_flag' => self::LIKE_FLAG_LIKED
            );
            $sql .= " ({$userLike['fb_uid']},{$userLike['object_id']},{$userLike{'like_flag'}},NOW(),NOW()),";
        }

        return array($sql, $count);

    }

    /**
     * Facebookでの新「いいね」しているユーザーを更新する
     * previousUrlを更新する
     *
     * @param $postId
     * @param $previousUrl
     * @return array
     */
    public function getNewLikesFacebookPagePost($postId, $previousUrl)
    {
        $likedUsers = array();
        $limit = self::FB_API_LIMIT_RECORD;

        if ($this->request_count >= self::FB_REQUEST_LIMIT) {
            $this->request_count = 0;
            sleep(600);
        }
        try {
            if ($previousUrl != null) {
                $request = "/{$postId}/likes?fields=id&limit={$limit}&before={$previousUrl}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    foreach ($response['data'] as $value) {
                        array_push($likedUsers, $value->id);
                    }
                    $previousUrl = $response['paging']->cursors->before;
                } else {
                    $request = "/{$postId}/likes?fields=id&limit={$limit}";
                    $response = $this->facebookApiClient->getResponse('GET', $request);
                    $this->request_count++;
                    $previousUrl = $response['paging']->cursors->before;
                }
            } else {
                $request = "/{$postId}/likes?fields=id&limit={$limit}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    foreach ($response['data'] as $value) {
                        array_push($likedUsers, $value->id);
                    }
                    $previousUrl = $response['paging']->cursors->before;
                }
            }
        } catch (Exception $e) {
            $this->logger->error('GetUsersLikesFbPage getNewLikesFacebookPagePost Error. $postId = ' . $postId . ' current request_count = ' . $this->request_count);
            $this->logger->error($e);
            return false;
        }

        return array($likedUsers, $previousUrl);
    }
}