<article class="modalInner-large jsTemplateSetting">
    <header class="tenmplateTitle">
        <h1>使用するテンプレートを選択してください</h1>
    </header>
    <section class="modalInner-cont">
        <section class="tenmplateListWrap jsCheckToggleWrap">
            <div class="tenmplateSelect">
                <div class="itemCategory">
                    <select name="inquiry_template_category_id" class="jsTemplateCategoryId">
                        <option value="0">選択してください</option>
                        <?php foreach ($data['inquiry_template_category_options'] as $inquiry_template_category_option) : ?>
                            <option
                                value="<?php assign($inquiry_template_category_option['id']); ?>"
                                <?php if ($inquiry_template_category_option['id'] == $data['inquiry_template']['inquiry_template_category_id']) {
                                    assign('selected');
                                } ?>>
                                <?php assign($inquiry_template_category_option['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- /.itemCategory --></div>
                <div class="itemTitle">
                    <select name="inquiry_template_id" class="selectItem jsTemplateId"
                            data-url="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'show_inquiry_template')); ?>">
                        <option value="0">選択してください</option>
                        <?php foreach ($data['inquiry_template_options'] as $inquiry_template_option) : ?>
                            <option
                                value="<?php assign($inquiry_template_option['id']); ?>"
                                <?php if ($inquiry_template_option['id'] == $data['inquiry_template']['id']) {
                                    assign('selected');
                                } ?>
                                data-inquiry_template_category_id="<?php assign($inquiry_template_option['inquiry_template_category_id']) ?>"
                                style="display: none;">
                                <?php assign($inquiry_template_option['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- /.itemTitle --></div>
                <div class="editBtn">

                    <span class="btn3">
                        <?php if ($data['inquiry_template']['id']): ?>
                            <a href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'edit_inquiry_template', array(), array(
                                'prev_page' => 'show_inquiry_template',
                                'inquiry_template_category_id' => $data['inquiry_template']['inquiry_template_category_id'],
                                'inquiry_template_id' => $data['inquiry_template']['id'],
                            ))); ?>"
                               class="small1">編集する</a>
                        <?php else: ?>
                            <span class="small1">編集する</span>
                        <?php endif; ?>
                    </span>
                    <!--/.editBtn --></div>
                <!-- /.editeArea --></div>
            <div class="tenmplateDetailWrap">
                <div class="tenmplateDetail">
                    <?php write_html($this->toHalfContentDeeply($data['inquiry_template']['content'])); ?>
                    <!-- /.tenmplateDetail --></div>
                <!-- /.tenmplateDetailWrap --></div>

        </section>
    </section>
    <footer>
        <p class="btnSet">
            <span class="btn2">
                <a href="javascript:void(0)" class="jsCloseTemplateModal"
                   data-close_modal_type="Template">キャンセル</a>
            </span>
            <span class="btn3">
                <a href="javascript:void(0)" class="jsTemplateSet" data-close_modal_type="Template"
                   data-content="<?php assign($data['inquiry_template']['content']); ?>">使用する</a>
            </span>
        </p>
    </footer>
</article>
