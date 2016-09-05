<?php
AAFW::import ( 'jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase' );
AAFW::import('jp.aainc.classes.CacheManager');
class edit_panel extends BrandcoPOSTActionBase {
	protected $ContainerName = 'edit_panel';

	protected $Form = array (
			'package' => 'admin-top',
			'action' => ''
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;
    private $file_info = array();

	protected $ValidatorDefinition = array (
			'panel_text' => array (
					'type' => 'str',
					'length' => 300
			),
            'panel_comment' => array(
                'type' => 'str',
                'length' => 300
            ),
			'image_url' => array (
					'type' => 'str',
					'length' => 512,
					'validator' => array (
							'URL'
					)
			),
			'brandSocialAccountId' => array (
					'required' => true
			),
			'entryId' => array (
					'required' => true
			),
			'panel_image' => array (
					'type' => 'file',
					'size' => '5MB'
			)
	);
	public function beforeValidate() {
		$this->Data ['brandSocialAccountId'] = $this->POST ['brandSocialAccountId'];
		$this->Data ['entryId'] = $this->POST ['entryId'];
		$this->Data ['service'] = $this->createService ( 'BrandSocialAccountService' );
		$this->Data ['brand'] = $this->getBrand();
        $this->Data ['stream'] = $this->Data ['service']->getStreamByBrandSocialAccountId ( $this->Data ['brandSocialAccountId'] );
        $this->Data ['service'] = $this->createService(get_class($this->Data ['stream']) . 'Service');

        $this->Data ['entry'] = $this->Data ['service']->getEntryById ( $this->Data ['entryId'] );

        if($this->Data ['entry']->getStoreName() == 'FacebookEntries') {
            $this->Form['action'] = 'edit_facebook_panel_form/' . $this->Data['brandSocialAccountId'] . '/' . $this->Data ['entryId'] . '?from=' . $this->from;
        }elseif($this->Data ['entry']->getStoreName() == 'TwitterEntries') {
            $this->Form['action'] = 'edit_twitter_panel_form/' . $this->Data['brandSocialAccountId'] . '/' . $this->Data ['entryId'] . '?from=' . $this->from;
        }elseif($this->Data ['entry']->getStoreName() == 'YoutubeEntries'){
            $this->Form['action'] = 'edit_youtube_panel_form/' . $this->Data['brandSocialAccountId'] . '/' . $this->Data ['entryId'] . '?from=' . $this->from;
        } elseif ($this->Data['entry']->getStoreName() == 'InstagramEntries') {
            $this->Form['action'] = 'edit_instagram_panel_form/' . $this->Data['brandSocialAccountId'] . '/' . $this->Data['entryId'] . '?from=' . $this->from;
        }
	}

	public function validate() {
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->Data ['brand']->id);
        if(!$idValidator->isCorrectEntryId($this->Data ['brandSocialAccountId'])) return false;
        if($this->Data ['stream']) {
            $idValidator = new StreamValidator(get_class($this->Data ['stream']) . 'Service', $this->Data ['brand']->id);
            if(!$idValidator->isCorrectEntryId($this->Data ['entryId'])) return false;

        }else{
            return false;
        }

		if ($this->FILES ['panel_image']) {
			$fileValidator = new FileValidator ( $this->FILES ['panel_image'],FileValidator::FILE_TYPE_IMAGE );
			if (!$fileValidator->isValidFile ()) {
				$this->Validator->setError('panel_image', 'NOT_MATCHES');
				return false;
			}else{
                $this->file_info = $fileValidator->getFileInfo();
            }
		}
		return true;
	}

	function doAction() {


        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

		if($this->Data['entry']->hidden_flg != $this->display){
			if($this->Data['entry']->priority_flg){
				$panel_service = $this->createService('TopPanelService');
			}else{
				$panel_service = $this->createService('NormalPanelService');
			}
			if($this->display == '0'){
				$panel_service->addEntry($this->Data['brand'],$this->Data['entry']);
			}else{
				$panel_service->deleteEntry($this->Data['brand'],$this->Data['entry']);
			}
		}

		// イメージをアップロード
		if ($this->FILES ['panel_image']) {
            $this->Data ['entry']->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'. $this->Data['brand']->id. '/'. $this->Data['entry']->getStoreName(). '/' . StorageClient::getUniqueId()), $this->file_info
            );
		}else{
			$this->Data ['entry']->image_url = $this->image_url;
		}

		$this->Data ['service']->updateEntryByPostObject ( $this->Data ['entry'], $this->POST );

		if ($this->Validator->getErrorCount ()) {
			$return = $this->getFormURL ();
		} else {
			$this->Data['saved'] = 1;
			if($this->from == 'top'){
				$return = 'redirect: ' . Util::rewriteUrl ( 'admin-top', 'edit_panel_list', array($this->Data ['brandSocialAccountId']), array('close' =>1,'refreshTop'=>1));
			}else{
				$return = 'redirect: ' . Util::rewriteUrl ( 'admin-top', 'edit_panel_list', array ($this->Data ['brandSocialAccountId']) );
			}
		}
		return $return;
	}
}
