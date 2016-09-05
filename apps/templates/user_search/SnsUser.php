<div class="col-md-12 col-md-offset-0">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">SNS連携情報</h3>
        </div>
        <table class="table table-bordered" style="table-layout: fixed; word-wrap: break-word;">
            <tbody>
            <tr>
                <th class="active" style="width: 140px">SNS</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td class="active" align="center"><img src="<?php assign($this->setVersion('/manager/img/' . SocialAccountService::$socialMiniIcon[$k] . '.png')) ?>" width="15" height="15" alt=""></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">連携状態</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center"><?php assign($data['social_accounts'][$k] ? '連携中' : '-') ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">プロフィール画像</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center">
                        <?php if($data['social_accounts'][$k]['profile_image_url']): ?>
                            <img src="<?php assign($data['social_accounts'][$k]['profile_image_url']) ?>" width="15" height="15" alt="">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">SNS UID</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center"><?php assign($data['social_accounts'][$k]['social_media_account_id'] ?: '-') ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">メールアドレス</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center"><?php assign($data['social_accounts'][$k]['mail_address'] ?: '-') ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">ユーザー名</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center"><?php assign($data['social_accounts'][$k]['name'] ?: '-') ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="active">アカウントリンク</th>
                <?php foreach(SocialAccountService::$userSearchSocialMedia as $k => $v): ?>
                    <td align="center">
                        <?php if($data['social_accounts'][$k]['profile_page_url']): ?>
                            <a href="<?php assign($data['social_accounts'][$k]['profile_page_url']) ?>" target="_blank">
                                <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>
