<div class="messageCodeInput">
    <p class="codeInput">
        <?php write_html($this->formText('code_auth_code', '', array($data['code_input_disabled'] => $data['code_input_disabled']))) ?>
        <?php if (!$data['is_locking_user']): ?>
            <span class="iconError1 jsCodeAuthCodeInputError" style="display:none"></span>
        <?php else: ?>
            <span class="iconError1 jsCodeAuthCodeInputError">コード認証が失敗しました。1時間後に再度試してください。</span>
        <?php endif ?>
    </p>
    <ul class="btnSet">
        <?php if (!$data['code_input_disabled']): ?>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsCodeAuthCodeInput" >登録</a></li>
        <?php else: ?>
            <li class="btn3"><span class="large1">登録</span></li>
        <?php endif ?>
        <!-- /.btnSet --></ul>
    <p class="codeAttention">
        <?php if($data['concrete_action']->min_code_flg == CpCodeAuthenticationAction::CODE_FLG_ON) write_html('<span class="unconfirm">あと<strong>' . ($data['remain_code_count']) . '</strong>個で次に進めます</span>') ?><span class="confirmed">認証済のコード（<strong><?php assign($data['code_auth_user_count']) ?></strong><?php if($data['concrete_action']->max_code_flg == CpCodeAuthenticationAction::CODE_FLG_ON) write_html('/' . $data['concrete_action']->max_code_count) ?>個）</span>
    </p>
    <?php if ($data['code_auth_users']): ?>
        <div class="codeListWrap">
            <table class="codeList">
                <caption></caption>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>コード</th>
                    <th>認証日時</th>
                </tr>
                </thead>
                <tbody>
                <?php $last_index = 1 ?>
                <?php foreach($data['code_auth_users'] as $code_auth_user): ?>
                    <tr>
                        <td><?php assign($last_index++) ?>.</td>
                        <td><?php assign($code_auth_user->getCodeAuthenticationCode()->code) ?></td>
                        <td><?php assign($code_auth_user->created_at) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($data['can_enter_code']): ?>
                    <tr class="moreCode">
                        <td><?php assign($last_index) ?>.</td>
                        <td>------------------</td>
                        <td>----/--/-- --:--</td>
                        <!-- /.moreCode --></tr>
                <?php endif ?>
                </tbody>
                <!-- /.codeList --></table>
            <!-- /.codeListWrap --></div>
    <?php endif ?>
    <!-- /.messageCodeInput --></div>
<div class="messageFooter">
    <ul class="btnSet">
        <?php if($data['is_action_clear'] && $data['is_not_join']): ?>
            <li class="btn3"><a href="javascript:void(0);" class="middle1 cmd_execute_code_auth_action">次へ</a></li>
        <?php else: ?>
            <li class="btn3"><span class="middle1">次へ</span></li>
        <?php endif ?>
        <!-- /.btnSet --></ul>
</div>