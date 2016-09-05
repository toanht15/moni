<div id="downloadModalArea">
<?php write_html(aafwWidgets::getInstance()->loadWidget('CpDataDownloadModal')->render(array(
    'brand_id' => $data['brand_id'],
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))); ?>
</div>
<div class="modal1 jsModal" id="modal_not_dl_announce">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p class="attention1"></p>
        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="small1">OK</a></span>
        </p>
    </section>
<!-- /.modal1 --></div>
