<div class="col-md-12 col-md-offset-0">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">BRANDCoブランド会員登録情報</h3>
        </div>
        <table class="table table-bordered" style="table-layout: auto; word-wrap: break-word;">
            <thead>
            <tr>
                <th class="active" style="width: 100px">ブランドID</th>
                <th class="active">ブランド名</th>
                <th class="active" style="width: 80px">会員番号</th>
                <th class="active" style="width: 160px">登録日時</th>
                <th class="active" style="width: 140px">メール配信状況</th>
                <th class="active" style="width: 160px">退会</th>
                <th class="active" style="width: 120px">代理ログイン</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['brand_users'] as $brand_user): ?>
                <tr>
                    <th scope="row"><?php assign($brand_user['brand_id']) ?></th>
                    <td><?php assign($brand_user['brand_name']) ?></td>
                    <td><?php assign($brand_user['no']) ?></td>
                    <td><?php assign($brand_user['created_at']) ?></td>
                    <td>
                        <form action="<?php assign(Util::rewriteUrl('users', 'set_optin')) ?>" method="POST">
                            <?php write_html($this->formHidden('relation_id', $brand_user['relation_id'])) ?>
                            <?php write_html($this->formHidden('return_url', urlencode(Util::getCurrentUrl()))) ?>
                            <?php assign($brand_user['optin_flg'] == BrandsUsersRelationService::STATUS_OPTIN ? '配信中' : '停止中') ?>
                            <?php if($brand_user['optin_flg'] == BrandsUsersRelationService::STATUS_OPTIN): ?>
                                <?php write_html($this->formHidden('new_optin_flg', BrandsUsersRelationService::STATUS_OPTOUT))?>
                                <button type="submit" class="btn btn-danger btn-xs jsConfirmAlert" data-message="本当に停止しますか？">停止する</button>
                            <?php else: ?>
                                <?php write_html($this->formHidden('new_optin_flg', BrandsUsersRelationService::STATUS_OPTIN))?>
                                <button type="submit" class="btn btn-primary btn-xs jsConfirmAlert" data-message="本当に配信しますか？">配信する</button>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td>
                        <?php if(!$brand_user['withdraw_flg']): ?>
                            <form action="<?php assign(Util::rewriteUrl('users', 'withdraw_brands')) ?>" method="POST">
                                <?php write_html($this->formHidden('relation_id', $brand_user['relation_id'])) ?>
                                <?php write_html($this->formHidden('return_url', urlencode(Util::getCurrentUrl()))) ?>
                                <button type="submit" class="btn btn-danger btn-xs jsConfirmAlert" data-message="本当に退会させますか？">退会する</button>
                            </form>
                        <?php else: ?>
                            <?php assign($brand_user['updated_at']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="<?php assign(Util::rewriteUrl('users', 'backdoor_login')) ?>" method="GET">
                            <?php write_html($this->formHidden('user_id', $brand_user['user_id'])) ?>
                            <?php write_html($this->formHidden('brand_id', $brand_user['brand_id'])) ?>
                            <?php write_html($this->formHidden('token', $brand_user['token'])) ?>
                            <?php foreach($data['parameter_data'] as $key => $val): ?>
                                <?php write_html($this->formHidden('parameter_'.$key, $val)); ?>
                            <?php endforeach;?>
                            <?php if(!$brand_user['withdraw_flg']): ?>
                                <button type="submit" class="btn btn-warning btn-xs" data-message="本当に代理ログインしますか？">代理ログイン</button>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
