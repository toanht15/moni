<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.batch.GetFacebookPagePostInfoBatchBase');

class GetUsersCmtsFbPagePost extends GetFacebookPagePostInfoBatchBase {

    public function executeProcess()
    {
        //すべてベージを取得する
        $entries = $this->getAllEntries();
        $detailCrawlerUrlService = $this->service_factory->create('DetailCrawlerUrlService');

        try {
            $accessToken = $this->getFacebookAccessToken($this->facebookUserTest['userId']);
            if (!$accessToken) {
                throw new aafwException('GetUsersCmtsFbPagePost Get Facebook Access Token failed! ');
            }

            $this->facebookApiClient->setToken($accessToken);
        } catch (Exception $e) {
            $this->logger->error('GetUsersCmtsFbPagePost Set Facebook AccessToken Error!');
            $this->logger->error($e);
            return;
        }

        $this->request_count = 0;
        foreach ($entries as $entry) {
            $count = 0;
            $sql = "";
            $crawlerUrl = $detailCrawlerUrlService->checkExistFbComment($entry['object_id'], $entry['type']);
            $nextUrl = ($crawlerUrl->url !== "") ? $crawlerUrl->url : "";

            while (1) {
                $result = $this->getCommentedUserFacebookPage($entry['post_id'], $nextUrl);
                if (!$result) break;

                list($commentsUsers, $nextUrl) = $result;

                if (count($commentsUsers) > 0) {
                    list($sql, $count) = $this->createSqlToUpdateUsersComments($commentsUsers, $count, $sql, $entry['object_id']);
                }

                if (count($commentsUsers) < self::FB_API_LIMIT_RECORD) {
                    if ($count > 0) {
                        $sql = substr($sql, 0, strlen($sql) - 1);
                        $sql .= " ON DUPLICATE KEY UPDATE
                        cmt_object_id = VALUES (cmt_object_id),
                        message = VALUES (message),
                        cmt_count = cmt_count + VALUES (cmt_count) ,
                        cmt_like_count = cmt_like_count + VALUES (cmt_like_count) ,
                        cmt_reply_count = cmt_reply_count + VALUES (cmt_reply_count),
                        updated_at = NOW()";
                        $this->db->executeUpdate($sql);
                    }
                    $this->updateDetailCrawlerUrl($entry['object_id'], $entry['type'], DetailCrawlerUrl::DATA_TYPE_COMMENT, $nextUrl);
                    break;
                }
            }
            continue;
        }
    }

    /**
     * 以前のコメントしたユーザー情報を取得する
     * @param $postId
     * @param $nextUrl
     * @return array
     */
    public function getCommentedUserFacebookPage($postId, $nextUrl)
    {
        $commentsUsers = array();
        $limit = self::FB_API_LIMIT_RECORD;

        if ($this->request_count >= self::FB_REQUEST_LIMIT) {
            $this->request_count = 0;
            sleep(600);
        }

        try {
            if ($nextUrl == "") {
                $request = "/{$postId}/comments?fields=id,from,message,like_count,comment_count&limit={$limit}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    $commentsUsers = $this->getCommentUserInfoFromGraphApiResponse($commentsUsers, $response['data']);
                    $nextUrl = $response['paging']->cursors->after;
                }
            } else {
                $request = "/{$postId}/comments?fields=id,from,message,like_count,comment_count&limit={$limit}&after={$nextUrl}";
                $response = $this->facebookApiClient->getResponse('GET', $request);
                $this->request_count++;

                if (isset($response['data'])) {
                    $commentsUsers = $this->getCommentUserInfoFromGraphApiResponse($commentsUsers, $response['data']);
                    $nextUrl = $response['paging']->cursors->after;
                } else {
                    // Update new next URL
                    $request = "/{$postId}/comments?fields=id,from,message,like_count,comment_count&limit={$limit}";
                    $response = $this->facebookApiClient->getResponse('GET', $request);
                    $this->request_count++;
                    $nextUrl = $response['paging']->cursors->after;
                }
            }

            return array($commentsUsers, $nextUrl);
        } catch (Exception $e) {
            $this->logger->error('GetUsersCmtsFbPagePost getCommentedUserFacebookPage Error. $postId = ' . $postId . ' current request_count = ' . $this->request_count);
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * Facebook Graph APIの出力データからコメントしたユーザー情報を取得する
     * @param $listCommentsUsers
     * @param $response_data
     * @return mixed
     */
    public function getCommentUserInfoFromGraphApiResponse($listCommentsUsers, $response_data)
    {
        foreach ($response_data as $data) {
            $commentsUser = array();

            $cmt_object_id = explode('_', $data->id);
            $commentsUser['cmt_id'] = (integer)$cmt_object_id[1];
            $commentsUser['fb_uid'] = (integer)$data->from->id;
            $commentsUser['cmt_count'] = 1;
            $commentsUser['message'] = json_encode(array("message"=>$data->message));
            $commentsUser['message'] = $this->escapeForSQL($commentsUser['message']);
            $commentsUser['cmt_like_count'] = $data->like_count;
            $commentsUser['cmt_reply_count'] = $data->comment_count;

            array_push($listCommentsUsers, $commentsUser);
        }

        return $listCommentsUsers;
    }

    /**
     * @param $query
     * @return mixed
     * @throws aafwException
     */
    private function escapeForSQL($query){
        $fbEntriesUsersComment = aafwEntityStoreFactory::create('FbEntriesUsersComments');
        $result = $fbEntriesUsersComment->escapeForSQL($query);

        return $result;
    }

    /**
     * コメントしたユーザー情報をデータベースに追加するSQLを作成する
     * @param $listCommentedUsers
     * @param $count
     * @param $sql
     * @param $object_id
     * @return array
     */
    public function createSqlToUpdateUsersComments($listCommentedUsers, $count, $sql, $object_id)
    {
        foreach ($listCommentedUsers as $cmt_user) {
            $count++;

            if ($count == 1) {
                $sql .= "INSERT INTO fb_entries_users_comments(fb_uid, cmt_object_id, object_id,cmt_count,message,cmt_like_count,cmt_reply_count,created_at, updated_at) VALUES";
            }

            $sql .= "({$cmt_user['fb_uid']},{$cmt_user['cmt_id']},{$object_id},{$cmt_user['cmt_count']},'{$cmt_user['message']}',{$cmt_user['cmt_like_count']},{$cmt_user['cmt_reply_count']},NOW(),NOW()),";
        }

        return array($sql, $count);
    }
}