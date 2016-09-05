<?php write_html($this->csrf_tag()); ?>
<div class="modal1 jsModal" id="SkeletonModal">
    <section class="modalCont-large jsModalCont">
        <iframe id="jsSkeletonModalIframe" data-src="<?php assign(Util::rewriteUrl('admin-cp', 'skeleton_modal', array(), array('cp_id' => $data['cp_id'])))?>" frameborder="0"></iframe>
    </section>
</div>

<?php write_html($this->scriptTag('admin-cp/CpDemoConfirmBoxService')); ?>
<?php write_html($this->scriptTag('admin-cp/EditSkeletonModal')); ?>