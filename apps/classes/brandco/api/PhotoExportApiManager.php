<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class PhotoExportApiManager extends ContentExportApiManagerBase {

    public function doSubProgress() {

        $db = new aafwDataBuilder();

        $param = array(
            'code' => $this->code,
            'max_id' => $this->max_id ? $this->max_id : null,
            'cp_action_type' => CpAction::TYPE_PHOTO
        );

        $pager = array(
            'page' => self::DEFAULT_PAGE,
            'count' => $this->limit + 1     // $photo_users_count = $page_limit + $next_min_user
        );

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $result = $db->getPhotoUsersByContentApiCodes($param, $order, $pager, true, 'PhotoUser');
        $photo_users = $result['list'];

        if (!$photo_users) {
            $json_data = $this->createResponseData('ng', array(), array('message' => '投稿フォトが存在しません'));
            return $json_data;
        }

        $api_code_service = $this->service_factory->create('ContentApiCodeService');

        // API Pagination
        $pagination = array();
        if ($result['pager']['count'] >= $this->limit + 1) {
            // If next_min_user is available pop it from photo_users list
            $last_photo_user = array_pop($photo_users);

            $pagination = array(
                'next_id' => $last_photo_user->id,
                'next_url'    => $api_code_service->getApiUrl($this->code, CpAction::TYPE_PHOTO, $last_photo_user->id, $this->limit)
            );
        }

        $response_data = $this->getApiExportData($photo_users, $this->getBrand());
        $json_data = $this->createResponseData('ok', $response_data, array(), $pagination);
        return $json_data;
    }

    /**
     * @param $export_data
     * @param null $brand
     * @return array
     */
    public function getApiExportData($export_data, $brand = null) {
        $data = array();
        $last_photo_user = null;

        foreach ($export_data as $photo_user) {
            $cur_data = array(
                'id' => $photo_user->id,
                'title' => $photo_user->photo_title,
                'comment' => $photo_user->photo_comment,
                'created_at' => $photo_user->created_at,
                'page_url' => $brand != null ? $photo_user->getPhotoDetailUrl($brand->id, $brand->directory_name) : ''
            );

            $photo_path = pathinfo($photo_user->photo_url);

            $cur_data['photos'] = array(
                'default' => array(
                    'url' => $photo_user->photo_url
                ),
                'medium' => array(
                    'url' => $photo_path['dirname'] . '/' . $photo_path['filename'] . '_m.' . $photo_path['extension']
                ),
                'square' => array(
                    'url' => $photo_path['dirname'] . '/' . $photo_path['filename'] . '_s.' . $photo_path['extension']
                )
            );

            $data[] = $cur_data;
        }

        return $data;
    }
}