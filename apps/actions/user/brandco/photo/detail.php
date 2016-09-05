<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.CampaignPageValidator');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class detail extends BrandcoGETActionBase {
    public $NeedRedirect = true;
    public $NeedOption = array();

    private $entry_id;
    /** @var PhotoStreamService $photo_stream_service */
    private $photo_stream_service;
    /** @var PhotoUserService $photo_user_service */
    private $photo_user_service;
    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;


    //404を返す対象のentry_id
    private $force_404_entry_ids = array(
        2133,
        2166,
        2187,
        2194,
        2211,
        2213,
        2509,
        2510,
        2537,
        2539,
        2545,
        2549,
        2563,
        2575,
        2578,
        2582,
        2583,
        2584,
        2585,
        2586,
        2589,
        2604,
        2615,
        2618,
        2625,
        2629,
        2644,
        2651,
        2656,
        2677,
        2682,
        2683,
        2718,
        2719,
        2721,
        2722,
        2737,
        2738,
        2743,
        2746,
        2748,
        2778,
        2781,
        2787,
        2788,
        2806,
        2808,
        2810,
        2814,
        2824,
        2826,
        2827,
        2831,
        2835,
        2840,
        2842,
        2843,
        2844,
        2846,
        2854,
        2863,
        2871,
        2881,
        2884,
        2886,
        2889,
        2892,
        2895,
        2896,
        2918,
        2940,
        2947,
        2950,
        2965,
        2978,
        2979,
        2983,
        2984,
        2989,
        2991,
        2993,
        2994,
        2995,
        3005,
        3010,
        3016,
        3022,
        3024,
        3031,
        3032,
        3037,
        3038,
        3048,
        3054,
        3055,
        3077,
        3079,
        3088,
        3102,
        3104,
        3113,
        3116,
        3129,
        3130,
        3131,
        3132,
        3133,
        3145,
        3148,
        3153,
        3155,
        3159,
        3168,
        3174,
        3192,
        3193,
        3194,
        3198,
        3200,
        3205,
        3206,
        3207,
        3215,
        3216,
        3217,
        3218,
        3219,
        3220,
        3225,
        3242,
        3243,
        3244,
        3245,
        3248,
        3283,
        3293,
        3317,
        3321,
        3324,
        3325,
        3326,
        3329,
        3336,
        3345,
        3346,
        3347,
        3362,
        3363,
        3364,
        3374,
        3380,
        3382,
        3388,
        3389,
        3399,
        3400,
        3411,
        3412,
        3413,
        3414,
        3429,
        3460,
        3462,
        3463,
        3464,
        3465,
        3466,
        3467,
        3468,
        3469,
        3470,
        3471,
        3478,
        3487,
        3507,
        3513,
        3528,
        3534,
        3555,
        3566,
        3572,
        3588,
        3590,
        3593,
        3598,
        3600,
        3605,
        3607,
        3628,
        3633,
        3638,
        3642,
        3653,
        3654,
        3656,
        3663,
        3664,
        3665,
        3677,
        3691,
        3703,
        3718,
        3719,
        3721,
        3722,
        3726,
        3746,
        3750,
        3771,
        3772,
        3778,
        3786,
        3800,
        3801,
        3805,
        3806,
        3812,
        3824,
        3833,
        3844,
        3847,
        3867,
        3868,
        3869,
        3870,
        3871,
        3888,
        3896,
        3897,
        3929,
        3946,
        3955,
        3982,
        4023,
        4075,
        4076,
        4077,
        4087,
        4141,
        4152,
        4169,
        4187,
        4190,
        4191,
        4214,
        4229,
        4238,
        4239,
        4262,
        4263,
        4264,
        4265,
        4266,
        4267,
        4268,
        4269,
        4270,
        4271,
        4279,
        4280,
        4283,
        4284,
        4285,
        4335,
        4353,
        4358,
        4359,
        4360,
        4361,
        4389,
        4394,
        4402,
        4403,
        4404,
        4406,
        4433,
        4436,
        4443,
        4449,
        4450,
        4489,
        4490,
        4495,
        4497,
        4506,
        4508,
        4509,
        4510,
        4517,
        4550,
        4573,
        4574,
        4589,
        4591,
        4600,
        4612,
        4613,
        4615,
        4713,
        4748,
        4750,
        4753,
        4807,
        5174,
        5385,
        5390,
        5497,
        6895,
        8241,
        8247,
        8249,
        8252,
        8254,
        8608,
        8677,
        10018,
        10123,
        10249,
        10419,
        10694,
        10709,
        11031,
        11039,
        12397,
        13043,
        14098,
        14359,
        15213,
        15580,
        17224,
        17288,
        17997,
        18195,
        19096,
        19099,
        19352,
        19462,
        19466,
        19467,
        19494,
        19497,
        19498,
        19500,
        19501,
        19504,
        19514,
        19521,
        19522,
        19523,
        19525,
        19526,
        19527,
        19528,
        19529,
        19530,
        19532,
        20761,
        20789,
        20812,
        20833,
        20837,
        20911,
        20913,
        21259,
        21261,
        21262,
        21263,
        21264,
        21265,
        21266,
        21267,
        21268,
        21269,
        21555,
        21664,
        21790,
        21933,
        21980,
        21987,
        21999,
        22015,
        22017,
        22034,
        22048,
        22113,
        22118,
        22125,
        22127,
        22198,
        22482,
        22813,
        22836,
        23208,
        23245,
        25469,
        25793,
        28663,
        29387,
        30474,
        30478,
        33570,
        35205,
        41062,
        42544,
        42732,
        45457,
        45655,
        46267,
        48684,
        48912,
        50604,
        50806,
        50812,
        50847,
        50848,
        51174,
        51195,
        51301,
        51347,
        51505,
        51546,
        51547,
        51548,
        51914,
        52127,
        52888,
        53187,
        53192,
        53342,
        53835,
        53951,
        53974,
        54007,
        54067,
        54155,
        54215,
        54275,
        54281,
        54283,
        54296,
        54313,
        54346,
        54347,
        54355,
        54360,
        54376,
        54476,
        54513
    );

    public function doThisFirst() {
        $this->entry_id = $this->GET['exts'][0];
    }

    public function beforeValidate() {
        $this->photo_stream_service = $this->createService('PhotoStreamService');
        $this->photo_user_service = $this->createService('PhotoUserService');
        $this->cp_flow_service = $this->createService('CpFlowService');
    }

    public function validate() {
        /**
         * TODO
         * 関連箇所を含めて後で削除する
         */
        if ($this->isForce404EntryId()) return '404';

        $photo_stream_validator = new StreamValidator('PhotoStreamService', $this->brand->id);
        if (!$photo_stream_validator->isCorrectEntryId($this->entry_id)) return '404';

        $this->photo_entry = $this->photo_stream_service->getEntryById($this->entry_id);
        if (!$this->photo_entry) return '404';

        $this->photo_user = $this->photo_entry->getPhotoUser();
        if ($this->photo_user->approval_status != PhotoUser::APPROVAL_STATUS_APPROVE) {
            $photo_user_share = $this->photo_user->getPhotoUserShare();
            if (!$photo_user_share) {
                return '404';
            }

            $this->limited = true;
        }

        return true;
    }

    public function doAction() {

        $cp_user = $this->photo_user->getCpUser();
        $this->user = $cp_user->getUser();
        $this->cp = CpInfoContainer::getInstance()->getCpById($cp_user->cp_id);

        $default_title = $this->getBrand()->name . ' / ' . $this->cp->getTitle();

        $this->Data['photo_entry'] = $this->photo_entry;
        $this->Data['page_data']['photo_user'] = $this->photo_user;
        $this->Data['page_data']['user'] = $this->user;
        $this->Data['page_data']['cp'] = $this->cp;
        $this->Data['page_data']['page_title'] = $default_title;

        /**
         * TODO
         * リプトンキャンペーンで文言によりシェアができなくなった為OG文言を固定した。
         * キャンペーン終了したら戻す
         */
        if ($this->cp->id == 3118) {
            $this->Data['pageStatus']['og']['title'] = 'リプトン　ハロウィンパーティーキャンペーン ハロウィン限定パッケージを撮影して応募！';
        } else {
            $this->Data['pageStatus']['og']['title'] = $default_title;
        }
        $this->Data['pageStatus']['og']['image'] = $this->photo_user->photo_url;
        $this->Data['pageStatus']['og']['url'] = Util::rewriteUrl('photo', 'detail', array($this->photo_entry->id));

        if (!$this->photo_user->photo_title && !$this->photo_user->photo_comment) {
            /**
             * TODO
             * リプトンキャンペーンで文言によりシェアができなくなった為OG文言を固定した。
             * キャンペーン終了したら戻す
             */
            if ($this->cp->id == 3118) {
                $this->Data['pageStatus']['og']['description'] = 'ギフト券でパーティーグッズをGETしよう！';
            } else {
                $this->Data['pageStatus']['og']['description'] = $this->cutLongText($this->cp->getTitle(), 30);
            }
        } else {
            $og_desc = '';
            if ($this->photo_user->photo_title) {
                $this->Data['page_data']['page_title'] = $this->photo_user->photo_title;
                $og_desc = $this->photo_user->photo_title . 'ー';
            }
            $this->Data['pageStatus']['og']['description'] = $this->cutLongText($og_desc . $this->photo_user->photo_comment, 30);
        }

        // ページング情報取得
        $params['approval_status'] = PhotoUser::APPROVAL_STATUS_APPROVE;
        if (!$this->limited) {
            $next_photo_user_id = $this->photo_user_service->getNextPhotoUserId($this->photo_user->id, $this->photo_user->cp_action_id, $params);

            if ($next_photo_user_id) {
                $next_photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($next_photo_user_id);
                $this->Data['page_data']['next_url'] = Util::rewriteUrl('photo', 'detail', array($next_photo_entry->id));
            }

            $prev_photo_user_id = $this->photo_user_service->getPrevPhotoUserId($this->photo_user->id, $this->photo_user->cp_action_id, $params);
            if ($prev_photo_user_id) {
                $prev_photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($prev_photo_user_id);
                $this->Data['page_data']['prev_url'] = Util::rewriteUrl('photo', 'detail', array($prev_photo_entry->id));
            }
        }

        // みんなの投稿取得
        $detail_page_limit = $this->photo_stream_service->getPageLimit(true);

        // キャンペーンのみんなの投稿があればそれを利用
        $photo_entries = $this->photo_user_service->getApprovedPhotoEntriesByActionId($this->photo_user->cp_action_id, 0, $detail_page_limit);

        $this->Data['page_data']['photo_entries'] = $this->limited ? array() : $photo_entries;

        if ($this->cp->status == Cp::STATUS_DEMO) {
            $this->Data["pageStatus"]["demo_info"]["is_demo_cp"] = true;
            $this->Data["pageStatus"]["demo_info"]["demo_cp_url"] = $this->cp->getDemoUrl();
            $this->Data["pageStatus"]["demo_info"]["cp_id"] = $this->cp->id;
            $this->Data["pageStatus"]["demo_info"]["isHideClearButton"] = true;
            $this->Data["pageStatus"]["demo_info"]["isHideDemoUrl"] = true;
        }

        $this->Data['brand_contract'] = BrandInfoContainer::getInstance()->getBrandContract();

        return 'user/brandco/photo/detail.php';
    }


    //404の対象かどうか判定
    public function isForce404EntryId() {
        return in_array($this->entry_id, $this->force_404_entry_ids);
    }
}