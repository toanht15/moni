<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント連携</title>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css')) ?>">
    <?php if(Util::isSmartPhone()):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
    <?php endif;?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.net.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.api.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.helper.js'))?>"></script>

    <!--[if lt IE 9]>
    <script src="<?php assign($this->setVersion('/js/html5shiv-printshiv.js')); ?>"></script>
    <![endif]-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?php assign($this->setVersion('/js/masonry.pkgd.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/min/imagesloaded.pkgd.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/_unit.js'))?>"></script>
    <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">

</head>
<body>
<div class="accountMerge">
    <header>
        <div class="wrap">
            <h1>今回応募に利用した連携アカウントを、既に登録済のアカウントに追加するとキャンペーンの参加に進めます。</h1>
            <div class="usedAccount">
                <h2>応募に利用したアカウント</h2>
                <p class="accountImg"><img src="<?php assign($data['fromAlliedUser']->socialAccounts[0]->profileImageUrl ? $data['fromAlliedUser']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/accountMerge/imgUser1.jpg'))?>" alt=""></p>
                <p class="accountInfo">
                    <span class="snsType"><?php assign($data['fromAlliedUser']->socialAccounts[0]->socialMediaType)?></span>
                    <span class="accountName"><?php assign($data['fromAlliedUser']->socialAccounts[0]->name)?></span>
                </p>
                <!-- /.usedAccount --></div>
            <!-- /.wrap --></div>
    </header>
    <article>
        <section class="accountMergeCont">
            <h2>入力された[<?php assign($data['toAlliedUser']->mailAddress)?>]は既に以下アカウントで利用・登録されています。追加手続きをお願いします。</h2>
            <div class="accountList">
                <div class="aaidAccount">
                    <div class="myAccount">
                        <p class="accountImg"><img src="<?php assign($data['toAlliedUser']->socialAccounts[0]->profileImageUrl ? $data['toAlliedUser']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/accountMerge/imgUser1.jpg') )?>" alt=""></p>
                        <p class="accountInfo">
                            <span class="name"><?php assign($data['toAlliedUser']->name)?></span>
                            <span class="mail"><?php assign($data['toAlliedUser']->mailAddress)?></span>
                            <!-- /.accountInfo --></p>
                        <!-- /.myAccount --></div>
                    <div class="myAccountDetail">
                        <div class="number">
                            <dl>
                                <dt>応募回数</dt>
                                <dd><?php assign($data['joinedCount'])?></dd>
                            </dl>
                            <!-- /.number --></div>
                        <!-- /.myAccountDetail --></div>
                    <!-- /.aaidAccount --></div>
                <?php if(count($data['toAlliedUser']->socialAccounts) > 0 ):?>
                <h3>連携済みのアカウント</h3>
                <?php endif;?>
                <ul class="linkAccountList">
                    <?php foreach ($data['toAlliedUser']->socialAccounts as  $socialAccount):?>
                        <li class="linkAccount">
                            <p class="accountImg"><img src="<?php assign($socialAccount->profileImageUrl ? $socialAccount->profileImageUrl : $this->setVersion('/img/accountMerge/imgUser1.jpg'))?>" alt=""></p>
                            <p class="accountInfo">
                                <span class="snsType"><?php assign($socialAccount->socialMediaType)?></span>
                                <span class="accountName"><?php assign($socialAccount->name)?></span>
                                <!-- /.accountInfo --></p>
                            <!-- /.linkAccount --></li>
                    <?php endforeach;?>
                    <li class="addAccount">
                        <p class="selectedAccount">応募に利用したアカウント</p>
                        <p class="accountImg"><img src="<?php assign($data['fromAlliedUser']->socialAccounts[0]->profileImageUrl ? $data['fromAlliedUser']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/accountMerge/imgUser1.jpg'))?>" alt=""></p>
                        <p class="accountInfo">
                            <span class="snsType"><?php assign($data['fromAlliedUser']->socialAccounts[0]->socialMediaType)?></span>
                            <span class="accountName"><?php assign($data['fromAlliedUser']->socialAccounts[0]->name)?></span>
                            <!-- /.accountInfo --></p>
                        <!-- /.addAccount --></li>
                </ul>

                <?php if($data['isFailure']):?>
                    <p class="errorText"><span>連携に失敗しました。</span>恐れ入りますが「アカウント追加連携画面にて失敗した旨を」こちらから<a href="https://allied-id.com/inquiry/inquiry">お問い合わせ</a>ください。</p>
                <?php else:?>
                    <form name="frmMerge" method="post" action="/account/execute_merge">
                        <?php write_html($this->csrf_tag()); ?>
                        <?php write_html($this->formHidden('token', $data['token'])); ?>
                        <p class="btnSet">
                            <span class="btn3"><a href="javascript:document.frmMerge.submit()" class="large3"><small>追加連携して</small>キャンペーン参加に進む</a></span>
                            <!-- /.btnSet --></p>
                    </form>
                <?php endif;?>
                <!-- /.accountList --></div>
            <div class="accountMergeAttention">
                <p>本ページ経由でアカウントを追加連携すると、キャンペーンの参加に進めます。<br>
                    今後、連携された各アカウントを利用してログイン・キャンペーン参加が可能になります。</p>
                <p class="supplement1">※本ご案内について不明点、お困りの点がある場合は「<a href="https://allied-id.com/inquiry/inquiry">お問い合わせ</a>」よりご連絡ください。</p>
                <!-- /.accountMergeAttention --></div>
            <!-- /.accountMergeCont --></section>
    </article>
    <!-- /.accountMerge --></div>
<footer>
    <ul class="links">
        <li><a href="#">モニプラ出展利用規約</a></li>
        <li><a href="#">アライドID利用規約</a></li>
        <li><a href="http://www.aainc.co.jp/privacy/" target="_blank">個人情報保護方針</a></li>
        <li><a href="http://allied-id.com/maintenance" target="_blank">メンテナンス情報</a></li>
        <!-- /.links --></ul>
    <p class="poweredBy"><small>Powered by monipla</small></p>
</footer>
</body>
</html>