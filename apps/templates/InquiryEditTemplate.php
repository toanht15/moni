<form action="<?php write_html(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'save_inquiry_template')); ?>" method="POST">
    <article class="modalInner-large jsTemplateSetting">
        <header class="tenmplateTitle">
            <h1>テンプレートを入力してください。</h1>
        </header>
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('prev_page', $data['prev_page'])); ?>
        <?php write_html($this->formHidden('inquiry_template_id', $data['inquiry_template']['id'])); ?>
        <section class="modalInner-cont scroll">
            <section class="tenmplateListWrap jsCheckToggleWrap">
                <div class="tenmplateSelect">
                    <div class="itemCategory">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('inquiry_template_category_id')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('inquiry_template_category_id')) ?></span>
                        <?php endif; ?>
                        <select name="inquiry_template_category_id">
                            <?php foreach ($data['inquiry_template_category_options'] as $inquiry_template_category_option) : ?>
                                <option value="<?php assign($inquiry_template_category_option->id); ?>" <?php if ($inquiry_template_category_option->id == $data['inquiry_template']['inquiry_template_category_id']) { assign('selected'); } ?>><?php assign($inquiry_template_category_option->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- /.itemCategory --></div>
                    <div class="itemTitle2">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('name')) ?></span>
                        <?php endif; ?>
                        <?php write_html($this->formText('name', $data['inquiry_template']['name'], array('placeholder' => 'タイトルを入力してください'))); ?>
                        <!-- /.itemTitle --></div>
                    <!-- /.editeArea --></div>

                <div class="tenmplateDetailWrap">
                    <?php if ($this->ActionError && !$this->ActionError->isValid('content')): ?>
                        <span class="iconError1"><?php assign($this->ActionError->getMessage('content')) ?></span>
                    <?php endif; ?>
                    <?php write_html($this->formTextarea('content', $data['inquiry_template']['content'], array('rows' => 13))); ?>
                    <!-- /.tenmplateDetailWrap --></div>
            </section>
        </section>
        <footer>
            <p class="btnSet">
                <span class="btn2"><a href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), $data['prev_page'])); ?>">前に戻る</a></span>
                <span class="btn3"><a href="javascript:void(0)" class="jsTemplateAdd">保存する</a></span>
            </p>
        </footer>
    </article>
</form>
