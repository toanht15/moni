<div class="modal1 jsModal" id="modalSectionAdd">
    <section class="modalCont-medium jsModalCont">
        <form action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), "api_add_inquiry_section.json")); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <h1 class="hd1">新しく登録するセクションを入力してください。</h1>
            <div class="sectionEditArea jsSectionEditArea">
                <select name="level"  name="jsSectionLevel">
                    <?php foreach (InquirySection::$level_options as $key => $val): ?>
                        <option value="<?php assign($key);?>"><?php assign($val); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="name" placeholder="セクション名を入力してください" class="inputSection jsSectionName">
                <input name="dummy" type="text" style="display: none;">
                <!-- /.editeArea --></div>
            <p class="btnSet">
                <span class="btn2"><a href="javascript:void(0)" class="jsCloseSectionModal" data-close_modal_type="SectionAdd">キャンセル</a></span>
                <span class="btn3"><a href="javascript:void(0)" class="jsSectionAdd" data-close_modal_type="SectionAdd">登録する</a></span>
            </p>
        </form>
    </section>
</div>