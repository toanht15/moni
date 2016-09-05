<article class="modalInner-large jsTemplateSetting">
    <header class="tenmplateTitle">
        <h1>テンプレート一覧</h1>
    </header>
    <section class="modalInner-cont scroll">
        <section class="tenmplateListWrap">
            <div class="tenmplateDetailWrap">

                <h2 class="hd2">カテゴリー作成</h2>

                <p class="categoryCreate">

                <form
                    action="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'save_inquiry_template_category')); ?>"
                    method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                        <span class="iconError1"><?php assign($this->ActionError->getMessage('name')) ?></span><br>
                    <?php endif; ?>
                    <span><?php write_html($this->formText('name', '', array('placeholder' => 'カテゴリー名', 'size' => 40))); ?></span>
                    <span class="btn3"><a href="javascript:void(0)" class="small1 jsTemplateCategoryAdd">作成</a></span>
                </form>
                <!-- /.categoryCreate --></p>

                <h2 class="hd2">カテゴリー一覧</h2>
                <ul class="categoryList1" style="margin-top:20px;">
                    <?php foreach ($data['inquiry_template_category_list'] as $category_order_no => $inquiry_template_category): ?>
                        <li>
                            <p class="category1">
                                <span
                                    class="categoryMove">順番を入れ替える</span><?php assign($inquiry_template_category['name']) ?>
                                <span class="categoryAction">
                                    <a href="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'edit_inquiry_template', array(), array(
                                        'prev_page' => 'show_inquiry_template_list',
                                        'inquiry_template_category_id' => $inquiry_template_category['id']))); ?>"
                                       class="iconBtnAdd">子カテゴリを追加する</a>
                                    <a href="javascript:void(0)"
                                       class="iconBtnNonDisplay jsOpenTemplateCategoryDeleteModal"
                                       data-open_modal_type="TemplateCategoryDelete"
                                       data-inquiry_template_category_id="<?php assign($inquiry_template_category['id']); ?>">カテゴリを削除する</a>
                                </span>
                                <!-- /.category1 --></p>
                            <ul class="categoryList1">
                                <?php foreach ($data['inquiry_template_list'][$inquiry_template_category['id']] as $inquiry_template): ?>
                                    <?php if ($inquiry_template['id']): ?>
                                        <li>
                                            <p class="category1">
                                                <span class="categoryMove">順番を入れ替える</span><a
                                                    href="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'edit_inquiry_template', array(), array(
                                                        'prev_page' => 'show_inquiry_template_list',
                                                        'inquiry_template_category_id' => $inquiry_template_category['id'],
                                                        'inquiry_template_id' => $inquiry_template['id']
                                                    ))); ?>"><?php assign($inquiry_template['name']) ?></a>
                                                <span class="categoryAction">
                                                    <a href="javascript:void(0)"
                                                       class="iconBtnNonDisplay jsOpenTemplateDeleteModal"
                                                       data-open_modal_type="TemplateDelete"
                                                       data-inquiry_template_id="<?php assign($inquiry_template['id']); ?>">カテゴリを削除する</a>
                                                </span>
                                                <!-- /.category1 --></p>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <!-- /.categoryList1 --></ul>
                        </li>
                    <?php endforeach; ?>
                    <!-- /.categoryList1 --></ul>
                <!-- /.tenmplateDetailWrap --></div>

        </section>
    </section>
    <footer>
        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="jsCloseTemplateModal"
                                  data-close_modal_type="Template">閉じる</a></span>
        </p>
    </footer>

    <div class="modal1 jsModal" id="modalTemplateCategoryDelete">
        <section class="modalCont-small jsModalCont">
            <form action="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'delete_inquiry_template_category')); ?>"
                  method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('inquiry_template_category_id', 0)); ?>
                <h1>本当に削除しますか？</h1>

                <p class="btnSet">
                    <span class="btn2"><a href="javascript:void(0)" class="small1 jsCloseTemplateCategoryDeleteModal"
                                          data-close_modal_type="TemplateCategoryDelete">キャンセル</a></span>
                    <span class="btn4"><a href="javascript:void(0)" class="small1 jsTemplateCategoryDelete"
                                          data-close_modal_type="TemplateCategoryDelete">削除</a></span>
                </p>

            </form>
        </section>
        <!-- /.modal1 --></div>

    <div class="modal1 jsModal" id="modalTemplateDelete">
        <section class="modalCont-small jsModalCont">
            <form action="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'delete_inquiry_template')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('inquiry_template_id', 0)); ?>
                <h1>本当に削除しますか？</h1>

                <p class="btnSet">
                    <span class="btn2"><a href="javascript:void(0)" class="small1 jsCloseTemplateDeleteModal"
                                          data-close_modal_type="TemplateDelete">キャンセル</a></span>
                    <span class="btn4"><a href="javascript:void(0)" class="small1 jsTemplateDelete"
                                          data-close_modal_type="TemplateDelete">削除</a></span>
                </p>

            </form>
        </section>
        <!-- /.modal1 --></div>

</article>
