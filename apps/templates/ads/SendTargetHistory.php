<div class="sendHistory">
    <dl class="conditionSegmentMeta">
        <dt>送信履歴</dt>
        <dd>
            <table class="customaudienceTable">
                <thead>
                <tr>
                    <th>送信日</th>
                    <th>対象数</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data['send_target_logs'] as $send_target_log): ?>
                    <tr>
                        <td><?php assign(date('Y/m/d', strtotime($send_target_log->created_at))) ?></td>
                        <td><strong><?php assign($send_target_log->total)?><span>名</span></strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <!-- /.customaudienceTable --></table>
        </dd>
    </dl>
<!-- /.sendHistory --></div>