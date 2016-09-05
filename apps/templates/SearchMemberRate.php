<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_RATE)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <ul>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_5.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::RATE_5] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_5.png')) ?>" width="24" height="24" alt=""> 5</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_4.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::RATE_4] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_4.png')) ?>" width="24" height="24" alt=""> 4</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_3.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::RATE_3] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_3.png')) ?>" width="24" height="24" alt=""> 3</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_2.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::RATE_2] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_2.png')) ?>" width="24" height="24" alt=""> 2</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_1.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::RATE_1] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_1.png')) ?>" width="24" height="24" alt=""> 1</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::NON_RATE.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::NON_RATE] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_0.png')) ?>" width="24" height="24" alt=""> 未評価</label></li>
        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::BLOCK.'/'.$this->search_no)?>' <?php assign($data['search_rate']['search_rate/'.BrandsUsersRelationService::BLOCK] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconBlockOn.png')) ?>" width="24" height="24" alt=""> ブロックユーザー</label></li>
    </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_RATE)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_RATE)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>