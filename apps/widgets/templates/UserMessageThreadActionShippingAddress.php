<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeShippingAddressActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_shipping_address_action.json")); ?>" method="POST" enctype="multipart/form-data" >

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>

        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>

        <?php write_html($this->formHidden('cp_shipping_address_action_id', $data['shipping_address_action']->id)); ?>

        <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::JOIN){
            $disable = 'disabled';
        } ?>
        <ul class="commonTableList1">
          <?php if($data['shipping_address_action']->name_required):?>
          <li>
            <p class="title1">
              <span class="require1">氏名（かな）</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <label class="editName"><span>姓</span><?php write_html( $this->formText('lastName', $data['userShippingAddress']['last_name'] ? $data['userShippingAddress']['last_name'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?></label><label class="editName"><span>名</span><?php write_html( $this->formText('firstName', $data['userShippingAddress']['first_name'] ? $data['userShippingAddress']['first_name'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?></label>
              </span>
              <span id="error_shipping_address_name" class="iconError1" style="display:none"></span>
              <span class="editInput">
                <label class="editName"><span>せい</span><?php write_html( $this->formText('lastNameKana', $data['userShippingAddress']['last_name_kana'] ? $data['userShippingAddress']['last_name_kana'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?></label><label class="editName"><span>めい</span><?php write_html( $this->formText('firstNameKana', $data['userShippingAddress']['first_name_kana'] ? $data['userShippingAddress']['first_name_kana'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?></label>
              <!-- /.editInput --></span>
              <span id="error_shipping_address_nameKana" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <?php endif;?>
          
          <?php if($data['shipping_address_action']->address_required):?>
          <li>
            <p class="title1">
              <span class="require1">郵便番号</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <?php write_html( $this->formText('zipCode1', ($data['userShippingAddress']['zip_code1'] ? $data['userShippingAddress']['zip_code1'] : PHPParser::ACTION_FORM), array('class' => 'inputNum', 'maxlength' => 3, $disable=>$disable) ));?>－<?php write_html( $this->formText('zipCode2', ($data['userShippingAddress']['zip_code2'] ? $data['userShippingAddress']['zip_code2'] : PHPParser::ACTION_FORM), array('class' => 'inputNum', 'maxlength' => 4, 'onKeyUp' => "AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');", $disable=>$disable) ))?>
                <a href="javascript:;" onclick="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');">住所検索</a><span class="supplement1">※半角数字</span>
              <!-- /.editInput --></span>
              <span id="error_shipping_address_zipCode" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <li>
            <p class="title1">
              <span class="require1">都道府県</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                  <?php write_html( $this->formSelect('prefId', $data['userShippingAddress']['pref_id'] ? $data['userShippingAddress']['pref_id'] : PHPParser::ACTION_FORM, array($disable=>$disable), $this->prefectures))?>
              <!-- /.editInput --></span>
              <span id="error_shipping_address_prefId" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <li>
            <p class="title1">
              <span class="require1">市区町村</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <?php write_html( $this->formText('address1', $data['userShippingAddress']['address1'] ? $data['userShippingAddress']['address1'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
              <!-- /.editInput --></span>
              <span id="error_shipping_address_address1" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <li>
            <p class="title1">
              <span class="require1">番地</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <?php write_html( $this->formText('address2', $data['userShippingAddress']['address2'] ? $data['userShippingAddress']['address2'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
              <!-- /.editInput --></span>
              <span id="error_shipping_address_address2" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <li>
            <p class="title1">
              建物
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <?php write_html( $this->formText('address3', $data['userShippingAddress']['address3'] ? $data['userShippingAddress']['address3'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
                <!-- /.editInput --></span>
              <span id="error_shipping_address_address3" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <?php endif;?>
          <?php if($data['shipping_address_action']->tel_required):?>
          <li>
            <p class="title1">
              <span class="require1">電話番号</span>
            <!-- /.title1 --></p>
            <p class="itemEdit">
              <span class="editInput">
                <?php write_html( $this->formTel('telNo1', $data['userShippingAddress']['tel_no1'] ? $data['userShippingAddress']['tel_no1'] : PHPParser::ACTION_FORM, array('class' => 'inputNum', $disable=>$disable) ))?>－<?php write_html( $this->formTel('telNo2', $data['userShippingAddress']['tel_no2'] ? $data['userShippingAddress']['tel_no2'] : PHPParser::ACTION_FORM, array('class' => 'inputNum', $disable=>$disable) ))?>－<?php write_html( $this->formTel('telNo3', $data['userShippingAddress']['tel_no3'] ? $data['userShippingAddress']['tel_no3'] : PHPParser::ACTION_FORM, array('class' => 'inputNum',$disable=>$disable) ))?>
              <!-- /.editInput --></span>
              <span class="supplement1">※半角数字</span>
              <span id="error_shipping_address_telNo" class="iconError1" style="display:none"></span>
            <!-- /.itemEdit --></p>
          </li>
          <?php endif;?>
        <!-- /.commonTableList1 --></ul>

        <div class="messageFooter">
            <ul class="btnSet">
            <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                <li class="btn3"><a class="cmd_execute_shipping_address_action large1" href="javascript:void(0)"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></a></li>
            <?php else: ?>
                <li class="btn3"><span class="large1">送信完了</span></li>
            <?php endif; ?>
            <!-- /.btnSet --></ul>
        </div>

    </form>

<!-- /.message --></section>
<script src="<?php assign(Util::getHttpProtocol()); ?>://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<?php write_html($this->scriptTag('user/UserActionShippingAddressService')); ?>
