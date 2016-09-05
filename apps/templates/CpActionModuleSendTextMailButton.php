<div class="moduleSettingWrap">
    <ul class="moduleSetting">
        <li><label><?php write_html($this->formCheckBox('send_text_mail_flg', array($this->getActionFormValue('send_text_mail_flg')), array($data['disable'] => $data['disable']), array('1' => 'HTML（本文表示）モード')));?></label>
  <span class="iconHelp">
    <span class="text">ヘルプ</span>
    <span class="textBalloon1">
      <span>下記の本文に入力された内容が<br>HTMLメールに組み込まれて配信されます。</span>
    <!-- /.textBalloon1 --></span>
  <!-- /.iconHelp --></span></li>
    </ul>
<!-- /.moduleSettingWrap --></div>

