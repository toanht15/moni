<aside class="drawerNaviWrap close jsModalTarget-menu" id="side-menu">
    <?php if($data['isSynCampaign']): ?>
        <div class='synapse-page-tracker' data-page-name='daily-lucky' data-campaign='daily-lucky'></div>
    <?php endif;?>
    <div class="drawerNavi" data-role="listview" data-inset="false" data-divider-theme="a">
        <?php if ( !($data['brand']->id == '452' || $data['brand']->id == '453') ): // TODO NECレノボグループ対応?>
            <?php if ($data['can_show_syn_menu']) : ?>
                <div id="adg_div">
                    <?php $adgId = $data['isNeedReplaceSynMenu'] ? '35264' : '31095';?>
                    <script src="<?php assign(Util::getHttpProtocol() === 'https' ? 'https://ssl.socdm.com/' : 'http://i.socdm.com/') ?>sdk/js/adg-script-loader.js?id=<?php assign($adgId);?>&amp;targetID=adg_<?php assign($adgId);?>&amp;displayid=1&amp;adType=FREE&amp;async=false&amp;tagver=2.0.0"></script>
                </div>

                <script>
                    window.addEventListener($adg.listener.loaded, function (e) {
                        var firstInView = false;
                        $('#adg_div').css('display', 'block');
                        $('#adg_div').on('inview', function(event, isInView){
                            if (isInView) {
                                if (!firstInView) {
                                    $adg.ads.trackShowEvent();
                                    firstInView = true;
                                }
                            }
                        });
                    });
                    window.addEventListener($adg.listener.failed, function () {
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <h2 class="hd2"><?php assign($data['brand']->hasOption(BrandOptions::OPTION_TOP) ? $data['brand']->name : 'モニプラ'); ?></h2>
        <ul class="privateMenu">
            <li class="home"><a href="<?php assign($data['brand']->hasOption(BrandOptions::OPTION_TOP) ? $data['brand']->getUrl() : Util::createApplicationUrl(config('Domain.monipla_media'))); ?>">トップページ</a></li>
            <?php if ($data['has_header_option']): ?>
                <?php foreach($data['globalMenus'] as $menu):?>
                    <li><a href="<?php assign($menu->link)?>"<?php if($menu->is_blank_flg):?> target="_blank"<?php endif;?>><?php assign($menu->name)?></a></li>
                <?php endforeach;?>
            <?php endif; ?>
        </ul>

        <?php if ( !($data['brand']->id == '452' || $data['brand']->id == '453') ): // TODO NECレノボグループ対応?>
            <?php if ($data['can_show_syn_menu']) : ?>
                <div id='synapse-service-list-outer-box' style='display: none'>
                    <?php write_html($this->csrf_tag())?>
                    <?php write_html($this->formHidden("cp_id", $data['cp']->id))?>
                    <h2 class="hd2">おすすめサービス</h2>
                    <ul id='synapse-service-list'>
                    </ul>
                </div>

                <div id="synapse-logo-box" class="synapse_logo" style='display: none'></div>
                <script>
                    var syn_menu_is_production = <?php assign(config('Stage') === 'product' ? 1 : 0); ?>;
                    var syn_menu_menu_name = "<?php assign(config('Stage') === 'product' ? 'monipla_cp_side_menu' : 'synapse_dev_side_menu'); ?>";
                    <?php if($data['isNeedReplaceSynMenu']):?>
                        syn_menu_menu_name = "syndot_campaign_side_menu";
                    <?php endif;?>
                    var syn_menu = null;
                </script>
                <script src="<?php write_html($this->setVersion("/js/syn/jquery.inview.min.js", false)); ?>"></script>
                <script src="<?php write_html($this->setVersion("/js/syn/unit_syn.js", false)); ?>"></script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</aside>
<div class="modalBase jsModalBase"></div>
