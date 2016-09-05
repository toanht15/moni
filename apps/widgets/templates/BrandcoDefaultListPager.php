<div class="pager1 jsListPager">
    <?php if ($this->TotalCount): ?>
        <p><?php assign($this->TotalCount) ?>件中<?php assign($this->Count * ($this->CurrentPage - 1) + 1) ?>件～<?php assign(($this->Count * $this->CurrentPage < $this->TotalCount) ? $this->Count * $this->CurrentPage : $this->TotalCount) ?>件表示しています</p>

        <?php if ($this->TotalCount > $this->Count): ?>
            <ul>
                <?php if ($this->CurrentPage > 1): /* << < */?>
                    <li class="first"><a href="javascript:void(0);" class="jsPager" data-page="1">最初のページヘ</a></li>
                    <li class="prev"><a href="javascript:void(0);" class="jsPager" data-page="<?php assign($this->CurrentPage - 1) ?>">前のページへ</a></li>
                <?Php endif; ?>
                <?php for ($i = $this->Start; $i <= $this->End; $i++): ?>
                    <?php if ($this->CurrentPage == $i): ?>
                        <li><span><?php assign($i) ?></span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" class="jsPager" data-page="<?php assign($i) ?>"><?php assign($i) ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($this->CurrentPage < $this->TotalPage): /* 99 > >> */?>
                    <li class="next"><a href="javascript:void(0)" class="jsPager" data-page="<?php assign($this->CurrentPage + 1) ?>">次のページへ</a></li>
                    <li class="last"><a href="javascript:void(0)" class="jsPager" data-page="<?php assign($this->TotalPage) ?>">最後のページヘ</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <?php if($this->order_kind_labels && $this->order_type_labels): ?>
            <p class="order">
                <?php write_html($this->formSelect('order_kind', $this->order_kind ?: 1, array('class' => 'jsOrderKind'),$this->order_kind_labels)) ?>&nbsp;
                <?php write_html($this->formRadio('order_type', $this->order_type ?: 1, array('class' => 'jsOrderType'), $this->order_type_labels)) ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>
</div>
