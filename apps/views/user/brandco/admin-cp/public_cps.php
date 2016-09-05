<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <article>
        <h1 class="hd1"><?php assign($data['type'] == Cp::TYPE_CAMPAIGN ? 'キャンペーン' : 'メッセージ');?>一覧</h1>

        <?php foreach($data['cps'] as $cp_key => $group_array): ?>
            <section class="campaignWrap jsPublicCpPage">

                <?php write_html($this->parseTemplate('ActionHeader.php',array(
                    'cp_id' => $cp_key,
                    'group_array' => $group_array,//必要なのはpublic_cpsの時だけ
                    'action_id' => $data['action_id'],
                    'user_list_page' =>  false,
                    'pageStatus' => $data['pageStatus'],
                    'enable_archive' => true,
                    'isHideDemoFunction' => $data['isHideDemoFunction']
                ))); ?>

            <!-- /.campaignWrap --></section>
        <?php endforeach; ?>

        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
            'TotalCount' => $data['totalEntriesCount'],
            'CurrentPage' => $this->params['p'],
            'Count' => $data['pageLimited'],
        ))) ?>
    </article>

<div class="modal2 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">このキャンペーンを削除しますか？</span></p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
            <span class="btn4"><a id="archive_button" href="javascript:void(0)" class="middle1">削除する</a></span>
        </p>
    </section>
</div>

<?php write_html($this->parseTemplate('CpDemoConfirmBoxTemplate.php')) ?>
<div id="downloadModalArea"></div>
<div class="modal1 jsModal" id="modal_not_dl_announce">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p class="attention1"></p>
        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="small1">OK</a></span>
        </p>
    </section>
<!-- /.modal1 --></div>

<?php $param =($data['pageStatus']['script'] = array('admin-cp/PublicCpsService', 'admin-cp/CpMenuService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
