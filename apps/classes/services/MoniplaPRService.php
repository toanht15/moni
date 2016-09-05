<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class MoniplaPRService extends aafwServiceBase {

    /** @var aafwApplicationConfig config */
    private $config;

    /** @var array */
    private $from_media_fids = array(
        'mpsplpc',  //スペシャル枠 PC
        'mpsplsp',  //スペシャル枠 SP
        'mphotpc',  //人気枠 PC
        'mphotsp',  //人気枠 SP
        'mppulpc',  //pickup大枠 PC
        'mppulsp',  //pickup大枠 SP
        'mppuspc',  //pickup小枠 PC
        'mppussp',  //pickup小枠 SP
        'mpnewpc',  //新着枠 PC
        'mpnewsp',  //新着枠 SP
        'mpwinpc',  //大量当選枠 PC
        'mpwinsp',  //大量当選枠 SP
        'mplstpc',  //キャンペーン一覧枠 PC
        'mplstsp',  //キャンペーン一覧枠 SP
        'mpnaip',   //ネイティブアプリ iOS
        'mpnaad',   //ネイティブアプリ Android
        'mpmlpr',   //モニプラメルマガPR枠
        'mppt',     //モニプラSNSアカウント投稿
        'mpml',     //モニプラメルマガ
        'mptxml',   //モニプラTEXTメルマガ
    );

    public function __construct() {
        parent::__construct();
        $this->config = aafwApplicationConfig::getInstance();
    }

    /**
     * モニプラメディアへの導線表示判定
     * @param Brand $brand
     * @param int $cp_id
     * @param string $fid
     * @return bool
     */
    public function canDisplayMoniplaLink($brand, $cp_id, $fid) {
        // PR禁止企業の場合は出力不可
        if ($brand->isDisallowedBrand()) {
            return false;
        }

        // TODO: 2016/03/16になったらfid配列、メソッド引数の$cp_id, $fid、app.ymlのMoniplaLinkCpId、及び以降の処理を削除し、return trueとする。
        // return true;

        // PRが常に許可されている企業の場合は出力
        if ($brand->isAlwaysAllowedBrand()) {
            return true;
        }

        $cp_id = intval($cp_id);

        // リリース前に開かれていたキャンペーンについては旧式（fidを見て出力を判定する方法）で判定する
        /** @var int $monipla_link_cp_id リリース時の最後のキャンペーンID */
        $monipla_link_cp_id = intval($this->config->query('MoniplaPR.MoniplaLinkCpId')) ?: 0;
        $is_before_release_cp = $cp_id <= $monipla_link_cp_id;
        if ($is_before_release_cp) {
            return in_array($fid, $this->from_media_fids);
        }

        return true;
    }

    public function isSmartPhone(){
        return Util::isSmartPhone();
    }

    public function findSynCp($cp_id){
        return $this->getModel('SynCps')->findOne(array('cp_id'=>$cp_id));
    }
}