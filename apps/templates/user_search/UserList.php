<div class="col-md-12 col-md-offset-0">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">検索結果が複数人います</h3>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Platform ID</th>
                <th>ニックネーム</th>
                <th>連携SNS</th>
                <th>メールアドレス</th>
                <th>詳細</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['users'] as $platform_user_id => $user): ?>
            <tr>
                <th scope="row"><?php assign($user['platform_user']['id']) ?></th>
                <td><?php assign($user['platform_user']['name']) ?></td>
                <td>
                    <?php if($data['platform_user']['social_accounts']): ?>
                        <?php foreach($data['platform_user']['social_accounts'] as $social_account): ?>
                            <img src="<?php assign($this->setVersion('/manager/img/' . $social_account['sns_mini_icon'] . '.png')) ?>" width="15" height="15" alt="">
                        <?php endforeach; ?>
                    <?php else: ?>
                        なし
                    <?php endif; ?>
                </td>
                <td><?php assign($user['platform_user']['mail_address']) ?></td>
                <td><button type="submit" class="btn btn-danger jsUserList" data-userId="<?php assign($platform_user_id) ?>">表示</button></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

