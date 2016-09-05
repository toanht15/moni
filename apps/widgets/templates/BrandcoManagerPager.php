<?php if ($this->TotalCount): ?>
<div>
    <p><?php assign($this->TotalCount) ?>件中<?php assign($this->Count * ($this->CurrentPage - 1) + 1) ?>件～<?php assign(($this->Count * $this->CurrentPage < $this->TotalCount) ? $this->Count * $this->CurrentPage : $this->TotalCount) ?>件表示しています</p>
<?php if ($this->TotalCount > $this->Count): ?>
    <ul class="pagination">
        <?php if ($this->CurrentPage > 1): /* << < */?>
            <li><a href="<?php assign(str_replace("\x0bpage\x0b", 1, $this->URLBase)) ?>">&laquo;</a></li>
        <?Php endif; ?>
        <?php for ($i = $this->Start; $i <= $this->End; $i++): ?>
            <?php if ($this->CurrentPage == $i): ?>
                <li class="active"><span><?php assign($i) ?></span></li>
            <?php else: ?>
                <li><a href="<?php assign(str_replace("\x0bpage\x0b", $i, $this->URLBase)) ?>"><?php assign($i) ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($this->CurrentPage < $this->TotalPage): /* 99 > >> */?>
            <li><a href="<?php assign(str_replace("\x0bpage\x0b", $this->TotalPage, $this->URLBase)) ?>">&raquo;</a></li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
</div>
<?php endif; ?>
