<div class="col-md-6 col-md-offset-0">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Allied IDアカウント情報</h3>
        </div>
        <table class="table table-bordered" style="table-layout: fixed; word-wrap: break-word;">
            <tbody>
            <tr>
                <th class="active" style="width: 140px">Platform ID</th>
                <td><?php assign($data['platform_user']['id']) ?></td>
            </tr>
            <tr>
                <th class="active">名前</th>
                <td><?php assign($data['platform_user']['name']) ?></td>
            </tr>
            <tr>
                <th class="active">メールアドレス</th>
                <td><?php assign($data['platform_user']['mail_address']) ?></td>
            </tr>
            <tr>
                <th class="active">連携SNS</th>
                <td>
                    <?php if($data['platform_user']['social_accounts']): ?>
                        <?php foreach($data['platform_user']['social_accounts'] as $social_account): ?>
                            <img src="<?php assign($this->setVersion('/manager/img/' . $social_account['sns_mini_icon'] . '.png')) ?>" width="15" height="15" alt="">
                        <?php endforeach; ?>
                    <?php else: ?>
                        なし
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th class="active">Allied IDメール配信状況</th>
                <td>
                    <form action="<?php assign(Util::rewriteUrl('users', 'set_optin_platform_user')) ?>" method="POST">
                        <?php assign($data['platform_user']['optin'] == ManagerUserSearchService::PLATFORM_USER_OPTIN ? '配信中' : '停止中') ?>
                        <?php if($data['platform_user']['optin'] == ManagerUserSearchService::PLATFORM_USER_OPTIN): ?>
                            <?php write_html($this->formHidden('new_optin_flg', strval(ManagerUserSearchService::PLATFORM_USER_OPTOUT)))?>
                            <button type="submit" class="btn btn-danger btn-xs jsConfirmAlert" data-message="本当に停止しますか？">停止する</button>
                        <?php else: ?>
                            <?php write_html($this->formHidden('new_optin_flg',ManagerUserSearchService::PLATFORM_USER_OPTIN))?>
                            <button type="submit" class="btn btn-primary btn-xs jsConfirmAlert" data-message="本当に配信しますか？">配信する</button>
                        <?php endif; ?>
                        <?php write_html($this->formHidden('platform_user_id', $data['platform_user']['id']))?>
                        <?php write_html($this->formHidden('return_url', urlencode(Util::getCurrentUrl()))) ?>
                        <?php write_html($this->formHidden('opt_in_type', "AAID")) ?>
                    </form>
                </td>
            </tr>
            <tr>
                <th class="active">メディアメール配信状況</th>
                <td>
                    <form action="<?php assign(Util::rewriteUrl('users', 'set_optin_platform_user')) ?>" method="POST">
                        <?php assign($data['platform_user']['mpfb_optin'] == ManagerUserSearchService::PLATFORM_USER_OPTIN ? '配信中' : '停止中') ?>
                        <?php if($data['platform_user']['mpfb_optin'] == ManagerUserSearchService::PLATFORM_USER_OPTIN): ?>
                            <?php write_html($this->formHidden('new_optin_flg', strval(ManagerUserSearchService::PLATFORM_USER_OPTOUT)))?>
                            <button type="submit" class="btn btn-danger btn-xs jsConfirmAlert" data-message="本当に停止しますか？">停止する</button>
                        <?php else: ?>
                            <?php write_html($this->formHidden('new_optin_flg',ManagerUserSearchService::PLATFORM_USER_OPTIN))?>
                            <button type="submit" class="btn btn-primary btn-xs jsConfirmAlert" data-message="本当に配信しますか？">配信する</button>
                        <?php endif; ?>
                        <?php write_html($this->formHidden('platform_user_id', $data['platform_user']['id']))?>
                        <?php write_html($this->formHidden('return_url', urlencode(Util::getCurrentUrl()))) ?>
                        <?php write_html($this->formHidden('opt_in_type', "MPFB")) ?>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
