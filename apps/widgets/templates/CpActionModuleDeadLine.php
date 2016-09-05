<?php if (!$data['is_permanent'] && !$data['cp_action']->isOpeningCpAction()): ?>
<?php
//------------------------------------------------------------------------------
// クライアントにも公開することになったので、コメントアウトしておく
// Manager権限のみ表示する
//if (!$data['is_login_manager']) {
//    return;
//}
//------------------------------------------------------------------------------

$timeHH=[];
for ($i = 0; $i < 24; $i++) {
    $h = sprintf('%02d', $i);
    $timeHH[$h] = $h;
}

$timeMM=[];
for ($i = 0; $i < 60; $i++) {
    $m = sprintf('%02d', $i);
    $timeMM[$m] = $m;
}

$close = '';
if ($this->cp_action->end_type === CpAction::END_TYPE_DEFAULT ||
    $this->cp_action->end_type === CpAction::END_TYPE_NONE) {
    // 期限設定のフォームはデフォルトで閉じる
    if (!$this->ActionError) {
        $close = 'close';
    } elseif ($this->ActionError->isValid('end_type') &&
        $this->ActionError->isValid('end_date') &&
        $this->ActionError->isValid('end_datetime')) {
        $close = 'close';
    }
}

$end_type = $this->getActionFormValue('end_type');
?>

<style>
.labelModeAllied { padding:5px; }
</style>
<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<section class="moduleCont1">
<h1 class="editDeadline1 jsModuleContTile <?php assign($close); ?>">本ステップの期限設定</h1>
  <?php if ($this->ActionError && !$this->ActionError->isValid('end_type')): ?>
      <p class="iconError1"><?php assign($this->ActionError->getMessage('end_type')) ?></p>
  <?php endif; ?>
  <div class="moduleSettingWrap jsModuleContTarget">
      <small>ステップの期限指定すると、期限後はそれ以降のステップに進むことができなくなります。</small>
    <ul class="moduleSetting">
    <li>
        <label>
            <input type="radio" name="end_type" value="1"
                <?php if (is_null($end_type)): ?>
                    <?php assign($data['end_type'] == 1 ? 'checked=checked' : ''); ?>
                <?php else: ?>
                    <?php assign($end_type == 1 ? 'checked=checked' : ''); ?>
                <?php endif ?>
                <?php assign($data['disable'] == 'disabled' ? 'disabled="disabled"' : ''); ?>
            >
            キャンペーン終了期間に合わせる
        </label><br /><small><?php assign($data['cp_end_date']); ?></small>
    </li>
    <li>
      <label>
          <input type="radio" name="end_type" value="2"
                <?php if (is_null($end_type)): ?>
                    <?php assign($data['end_type'] == 2 ? 'checked=checked' : ''); ?>
                <?php else: ?>
                    <?php assign($end_type == 2 ? 'checked=checked' : ''); ?>
                <?php endif ?>
              <?php assign($data['disable'] == 'disabled' ? 'disabled="disabled"' : ''); ?>
          >
          期限日時を指定する
      </label><br />〜&nbsp;
      <?php if ($this->ActionError && !$this->ActionError->isValid('end_date')): ?>
            <p class="iconError1"><?php assign($this->ActionError->getMessage('end_date')) ?></p>
      <?php endif; ?>
      <?php if ($this->ActionError && !$this->ActionError->isValid('end_datetime')): ?>
            <p class="iconError1">
                <?php assign(str_replace('<%time>', '締め切り日時', $this->ActionError->getMessage('end_datetime')) )?>
            </p>
      <?php endif; ?>
      <?php write_html(
          $this->formText(
              'end_date',
              PHPParser::ACTION_FORM,
              [
                  'maxlength' => '10',
                  'class' => 'jsDate inputDate',
                  'placeholder' => '年/月/日',
                  'style' => 'width:98px',
                  'disabled' => $data['disable']
              ]
          )
      ); ?>
      <?php write_html($this->formSelect(
              'end_hh',
              PHPParser::ACTION_FORM,
              [ 
                  'class' =>'inputTime',
                  'style' => 'width:60px',
                  'disabled' => $data['disable']
              ],
              $timeHH
          )
      ); ?><span class="coron">:</span>
      <?php write_html($this->formSelect(
              'end_mm',
              PHPParser::ACTION_FORM,
              [
                  'class' => 'inputTime',
                  'style' => 'width:60px',
                  'disabled' => $data['disable']
              ],
              $timeMM
          )
      ); ?>
    </li>
    <li>
        <label>
            <input type="radio" name="end_type" value="0"
                <?php if (is_null($end_type)): ?>
                    <?php assign($data['end_type'] == 0 ? 'checked=checked' : ''); ?>
                <?php else: ?>
                    <?php assign($end_type == 0 ? 'checked=checked' : ''); ?>
                <?php endif ?>
                <?php assign($data['disable'] == 'disabled' ? 'disabled="disabled"' : ''); ?>
            >
            指定しない
        </label>
    </li>
   <!-- /.moduleSetting --></ul>
  <!-- /.moduleSettingWrap --></div>
<!-- /.moduleCont1 --></section>
<?php endif ?>