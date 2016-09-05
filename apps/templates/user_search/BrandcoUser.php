<div class="col-md-6 col-md-offset-0">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">BRANDCoアカウント情報</h3>
        </div>
        <table class="table table-bordered" style="table-layout: fixed; word-wrap: break-word;">
            <tbody>
            <tr>
                <th class="active" style="width: 140px">BRANDCo UID</th>
                <td><?php assign($data['brandco_user']['id']) ?></td>
            </tr>
            <tr>
                <th class="active">名前</th>
                <td><?php assign($data['brandco_user']['name']) ?></td>
            </tr>
            <tr>
                <th class="active">メールアドレス</th>
                <td><?php assign($data['brandco_user']['mail_address']) ?></td>
            </tr>
            <tr>
                <th class="active">登録日時</th>
                <td><?php assign($data['brandco_user']['created_at']) ?></td>
            </tr>
            <tr>
                <th class="active">退会</th>
                <td>
                    <form action="<?php assign(Util::rewriteUrl('users', 'withdraw_brands')) ?>" method="POST">
                        <?php write_html($this->formHidden('brandco_user_id', $data['brandco_user']['id'])) ?>
                        <?php write_html($this->formHidden('return_url', urlencode(Util::getCurrentUrl()))) ?>
                        <button type="submit" class="btn btn-danger btn-xs jsConfirmAlert" data-message="本当に退会させますか？">全ブランドを退会する</button>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
