<div class="modal1 jsModal" id="modalSectionDelete">
    <section class="modalCont-medium jsModalCont">
        <form action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), "api_delete_inquiry_section.json")); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <h1 class="hd1">削除するセクションを選択してください。</h1>
            <div class="sectionEditArea jsSectionEditArea">
                <select name="level" class="jsSectionLevel">
                    <?php foreach (InquirySection::$level_options as $key => $val): ?>
                        <option value="<?php assign($key);?>"><?php assign($val); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="inquiry_section_id" class="selectItem jsSectionId">
                    <option value="0">選択してください</option>
                </select>
                <!-- /.editeArea --></div>
            <p class="btnSet">
                <span class="btn2"><a href="javascript:void(0)" class="jsCloseSectionModal" data-close_modal_type="SectionDelete">キャンセル</a></span>
                <span class="btn4"><a href="javascript:void(0)" class="jsSectionDelete" data-close_modal_type="SectionDelete">削除する</a></span>
            </p>
        </form>
    </section>
</div>