<?php write_html($this->parseTemplate('BrandcoPopupHeader.php', $data['pageStatus'])); ?>

<article>
    <h1 class="hd1">用語説明</h1>

    <section class="glossaryListWrap">
        <h2 class="hd2">すべてのキャンペーンに表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>日付</th>
                <td>キャンペーンの開催期間のみ表示</td>
            <tr>
                <th>参加者数(全体)</th>
                <td>キャンペーン全体の参加者数（STEP1を完了したユーザ数）</td>
            </tr>
            <tr>
                <th>参加者数(PCのみ)</th>
                <td>PCより参加登録したキャンペーンの参加者数</td>
            </tr>
            <tr>
                <th>参加者数(モバイルのみ)</th>
                <td>モバイルより参加登録したキャンペーンの参加者数</td>
            </tr>
            <tr>
                <th>参加者数(新規会員のみ)</th>
                <td>当キャンペーンでブランドに新規登録したの参加者数</td>
            </tr>
            <tr>
                <th>参加者数(既存会員のみ)</th>
                <td>当キャンペーン以前にブランドに登録済みの参加者数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>

    <section class="glossaryListWrap">
        <h2 class="hd2">2016年4月1日以降に開催されたキャンペーンのみ表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>キャンペーントップPV(全体)</th>
                <td>キャンペーントップページ（<?php assign($data['cp']->getUrl())?>）のページビュー数</td>
            </tr>
            <tr>
                <th>キャンペーントップPV(PCのみ)</th>
                <td>PC経由でのキャンペーントップページ（<?php assign($data['cp']->getUrl())?>）のページビュー数</td>
            </tr>
            <tr>
                <th>キャンペーントップPV(モバイルのみ)</th>
                <td>モバイル経由でのキャンペーントップページ（<?php assign($data['cp']->getUrl())?>）のページビュー数</td>
            </tr>
            <tr>
                <th>キャンペーントップUU(全体)</th>
                <td>キャンペーントップページ（<?php assign($data['cp']->getUrl())?>）のユニークユーザ数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>
    <?php if($data['is_use_lp_page']): ?>
        <section class="glossaryListWrap">
            <h2 class="hd2">ランディングページを設定しているキャンペーンのみ表示される項目</h2>

            <table class="glossaryList1">
                <tbody>
                <tr>
                    <th>キャンペーンLPPV(全体)</th>
                    <td>キャンペーンランディングページ（<?php assign($data['cp']->getReferenceUrl())?>）のページビュー数</td>
                </tr>
                <tr>
                    <th>キャンペーンLPPV(PCのみ)</th>
                    <td>PC経由でのキャンペーンランディングページ（<?php assign($data['cp']->getReferenceUrl())?>）のページビュー数</td>
                </tr>
                <tr>
                    <th>キャンペーンLPPV(モバイルのみ)</th>
                    <td>モバイル経由でのキャンペーンランディングページ（<?php assign($data['cp']->getReferenceUrl())?>）のページビュー数</td>
                </tr>
                <tr>
                    <th>キャンペーンLPUU(全体)</th>
                    <td>キャンペーンランディングページ（<?php assign($data['cp']->getReferenceUrl())?>）のユニークユーザ数</td>
                </tr>
                </tbody>
                <!-- /.fileList1 --></table>
            <!-- /.fileListWrap --></section>
    <?php endif; ?>

    <section class="glossaryListWrap">
        <h2 class="hd2">Facebookいいね！モジュールを使用している場合に表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>新規Facebookいいね！数(ページ名)</th>
                <td>キャンペーンのステップで、いいね！ボタンが押下された回数</td>
            </tr>
            <tr>
                <th>既存Facebookいいね！数(ページ名)</th>
                <td>キャンペーンのステップで、すでにいいね！済みであった回数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>

    <section class="glossaryListWrap">
        <h2 class="hd2">Twitterフォローモジュールを使用している場合に表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>新規Twitterフォロー数(アカウント名)</th>
                <td>キャンペーンのステップで、フォローボタンが押下された回数</td>
            </tr>
            <tr>
                <th>既存Twitterフォロー数(アカウント名)</th>
                <td>キャンペーンのステップで、すでにフォロー済みであった回数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>

    <section class="glossaryListWrap">
        <h2 class="hd2">Instagramフォローモジュールを使用している場合に表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>新規Instagramフォロー数(アカウント名)</th>
                <td>キャンペーンのステップで、フォローボタンが押下された回数</td>
            </tr>
            <tr>
                <th>既存Instagramフォロー数(アカウント名)</th>
                <td>キャンペーンのステップで、すでにフォロー済みであった回数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>

    <section class="glossaryListWrap">
        <h2 class="hd2">YouTubeチャンネル登録モジュールを使用している場合に表示される項目</h2>

        <table class="glossaryList1">
            <tbody>
            <tr>
                <th>新規チャンネル登録数(アカウント名)</th>
                <td>キャンペーンのステップで、チャンネル登録ボタンが押下された回数</td>
            </tr>
            <tr>
                <th>既存チャンネル登録数(アカウント名)</th>
                <td>キャンペーンのステップで、すでにチャンネル登録済みであった回数</td>
            </tr>
            </tbody>
            <!-- /.fileList1 --></table>
        <!-- /.fileListWrap --></section>
</article>

<?php write_html($this->parseTemplate('BrandcoPopupFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-blog/FileListService'))))); ?>
