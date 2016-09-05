<?php
    $html_template = '<li';

    $service_factory = new aafwServiceFactory();

    /** @var SegmentCreateSqlService $segment_create_sql_service */
    $segment_create_sql_service = $service_factory->create("SegmentCreateSqlService");

    $not_flg = Util::isNullOrEmpty($data['condition']['not_flg']) ? false : true;

    $condition_text = $segment_create_sql_service->getShortenConditionText($data['condition']);

    reset( $data['condition'] );
    $first_key = key( $data['condition'] );

    if (strpos($first_key, 'search_social_account/') !== false || strpos($first_key, 'search_friend_count/') !== false) {
        foreach (SocialAccountService::$availableSocialAccount as $social_id) {
            if (strpos($first_key, 'search_social_account/' . $social_id . '/') === false && strpos($first_key, 'search_friend_count/' . $social_id) === false) {
                continue;
            }

            $html_template .= ' class="' . SocialAccountService::$socialBigIcon[$social_id];

            if ($data['or_condition_flg']) {
                $html_template .= ' or">';
            } elseif($not_flg){
                $html_template .= ' not">';
            } else {
                $html_template .= '">';
            }

            if ($data['or_condition_flg']) {
                $html_template .= '<p class="labelOr" style="display: inline;"><span>or</span></p>';
            }

            if($not_flg){
                $html_template .= '<p class="labelNot" style="display: inline;"><span>not</span></p>';
            }

            $html_template .= $condition_text . '</li>';
            break;
        }
    } else {

        if ($data['or_condition_flg']) {
            $html_template .= ' class="or">';
        } elseif ($not_flg) {
            $html_template .= ' class="not">';
        } else {
            $html_template .= '>';
        }

        if ($data['or_condition_flg']) {
            $html_template .= '<p class="labelOr"><span>or</span></p>';
        }

        if($not_flg){
            $html_template .= '<p class="labelNot"><span>not</span></p>';
        }

        $html_template .= $condition_text . '</li>';
    }

    write_html($html_template);