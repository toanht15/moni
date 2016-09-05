<div class="col-md-12 col-md-offset-0">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">BRANDCoキャンペーン参加情報 <small>参加数: <?php assign(count($data['cp_users'])) ?></small></h3>
        </div>
        <table class="table table-bordered" style="table-layout: auto; word-wrap: break-word;">
            <thead>
            <tr>
                <th class="active" style="width: 60px">CPID</th>
                <th class="active">キャンペーン名</th>
                <th class="active">ブランド</th>
                <th class="active" style="width: 80px">会員番号</th>
                <th class="active" style="width: 200px">開催期間</th>
                <th class="active" style="width: 160px">当選発表</th>
                <th class="active" style="width: 160px">参加日時</th>
                <th class="active">参加状況</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['cp_users'] as $cp_user_id => $cp_user): ?>
                <tr>
                    <th scope="row"><?php assign($cp_user['cp_id']) ?></th>
                    <td><a href="<?php assign(Util::getBrandBaseUrl($cp_user['brand_id'], $cp_user['directory_name'])
                            . 'campaigns/' . $cp_user['cp_id']) ?>" target="_blank"><?php assign($cp_user['title']) ?></a>
                    </td>
                    <td><?php assign($cp_user['brand_name']) ?> <a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' => Util::getBrandBaseUrl($cp_user['brand_id'], $cp_user['directory_name']))))?>my/login" target="_blank"><span class="glyphicon glyphicon-log-in"></span></a><br></td>
                    <td><?php assign($cp_user['no']) ?></td>
                    <td>
                        開始: <?php assign($cp_user['public_date']) ?><br>
                        終了: <?php assign($cp_user['end_date']) ?>
                    </td>
                    <td><?php assign($cp_user['shipping_method'] ? '発送をもって発表' : $cp_user['announce_date']) ?></td>
                    <td><?php assign($data['cp_user_statuses'][$cp_user['cp_id']]['join_date']? $data['cp_user_statuses'][$cp_user['cp_id']]['join_date'] : '-' ) ?></td>
                    <?php if ($data['cp_user_statuses'][$cp_user['cp_id']]['last_join_order_no']): ?>
                    <td>STEP<?php assign($data['cp_user_statuses'][$cp_user['cp_id']]['last_join_order_no']) ?><br><a href="<?php assign(Util::getBrandBaseUrl($cp_user['brand_id'], $cp_user['directory_name'])
                            . 'admin-cp/' .'edit_action/' . $cp_user['cp_id'] . '/' .$data['cp_user_statuses'][$cp_user['cp_id']]['cp_action_id'] ) ?>" target="_blank"><?php assign($data['cp_user_statuses'][$cp_user['cp_id']]['last_join_action_type'])?></a>まで完了</td>
                    <?php else: ?>
                    <td><?php assign("プロフィールアンケートで離脱")?></td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
