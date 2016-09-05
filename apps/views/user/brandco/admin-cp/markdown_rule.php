<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>文字の装飾について（Markdown記法）</title>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">
</head>
<body class="markdownListWrap">
<article>
    <h1 class="hd1">文字の装飾について（Markdown記法）</h1>
    <table class="markdownList">
        <thead>
        <tr>
            <th>ルール</th>
            <th>記述形式</th>
            <th>表示形式</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th rowspan="3">改行</th>
            <td><pre>あいうえおかきくけこ[改行][改行]<br>さしすせそたちつてと[改行][改行]<br>なにぬねのはひふへほ[改行][改行]</pre></td>
            <td class="messageText"><p>あいうえおかきくけこ</p><p>さしすせそたちつてと</p><p>なにぬねのはひふへほ</p>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <td><pre>あいうえおかきくけこ[半角空白][半角空白][改行]<br>さしすせそたちつてと[半角空白][半角空白][改行]<br>なにぬねのはひふへほ[半角空白][半角空白][改行]</pre></td>
            <td class="messageText"><p>あいうえおかきくけこ<br>さしすせそたちつてと<br>なにぬねのはひふへほ</p>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <td><pre>あいうえおかきくけこ[改行][改行]<br>[全角空白][全角空白][改行][改行]<br>さしすせそたちつてと<br>※空白行を2行以上入れたい場合</pre></td>
            <td class="messageText"><p>あいうえおかきくけこ</p><p>　　</p><p>さしすせそたちつてと</p>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>見出し</th>
            <td>
                <pre>
# キャンペーン名 H1

## キャンペーン名 H2

### キャンペーン名 H3

#### キャンペーン名 H4

##### キャンペーン名 H5

###### キャンペーン名 H6
                </pre>
            </td>
            <td class="messageText">
                <h1>キャンペーン名 H1</h1><h2>キャンペーン名 H2</h2><h3>キャンペーン名 H3</h3><h4>キャンペーン名 H4</h4><h5>キャンペーン名 H5</h5><h6>キャンペーン名 H6</h6>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>引用</th>
            <td>
                <pre>>キャンペーンにご参加いただいた方の中から抽選で100名様に ○○○をプレゼント！</pre>
            </td>
            <td class="messageText">
                <blockquote><p>キャンペーンにご参加いただいた方の中から抽選で100名様に ○○○をプレゼント！</p></blockquote>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>リスト</th>
            <td>
                        <pre>* タイトル 1
* タイトル 2
* タイトル 3
    * タイトル 3a
    * タイトル 3b
    * タイトル 3c

1. ステップ 1
2. ステップ 2
3. ステップ 3
    1. ステップ 3a
    2. ステップ 3b
    3. ステップ 3c
                        </pre>
            </td>
            <td class="messageText">
                <ul>
                    <li>タイトル 1</li>
                    <li>タイトル 2</li>
                    <li>タイトル 3
                        <ul>
                            <li>タイトル 3a</li>
                            <li>タイトル 3b</li>
                            <li>タイトル 3c</li>
                        </ul>
                    </li>
                </ul>
                <ol>
                    <li>ステップ 1</li>
                    <li>ステップ 2</li>
                    <li>ステップ 3
                        <ol>
                            <li>ステップ 3a</li>
                            <li>ステップ 3b</li>
                            <li>ステップ 3c</li>
                        </ol>
                    </li>
                </ol>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>罫線</th>
            <td>
                        <pre>* * *

***

*****</pre>
            </td>
            <td class="messageText">
                <hr><br>
                <hr><br>
                <hr><br>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>リンク</th>
            <td>
                <pre>これは [Google](http://google.com "Google") へのリンクです。
これは [Google](http://google.com) へのリンクです。
</pre>
            </td>
            <td class="messageText">
                これは <a href="javascript:;" title="Google">google</a> へのリンクです。<br>
                これは <a href="javascript:;" title="Google">google</a> へのリンクです。
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>強調</th>
            <td>
                        <pre>**これは太字です**</pre>
            </td>
            <td class="messageText">
                <strong>これは太字です</strong>
                <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>画像</th>
            <td>
                        <pre>![代替テキスト](https:<?php assign(config('Static.Url'))?>/img/base/imgLogo_g.png)

![代替テキスト](https:<?php assign(config('Static.Url'))?>/img/base/imgLogo_g.png "画像タイトル")

![代替テキスト](https:<?php assign(config('Static.Url'))?>/img/base/imgLogo_g.png "画像タイトル" "横幅-高さ")</pre>
            </td>
            <td class="messageText">
                <img src="<?php assign($this->setVersion('/img/base/imgLogo_g.png'))?>" width="111" height="18" alt="Alt text"><br>
                <img src="<?php assign($this->setVersion('/img/base/imgLogo_g.png'))?>" width="111" height="18" alt="Alt text" title="画像タイトル"><br>
                <img src="<?php assign($this->setVersion('/img/base/imgLogo_g.png'))?>" width="166" height="27" alt="Alt text" title="画像タイトル"><br>
            <!-- /.messageText --></td>
        </tr>
        <tr>
            <th>リンク画像</th>
            <td>
<pre>これは [![img](https:<?php assign(config('Static.Url'))?>/img/base/imgLogo_g.png)](リンク先のURL) のリンクです。</pre>
            </td>
            <td class="messageText">
                これは <a href="javascript:;" title="Google"><img src="<?php assign($this->setVersion('/img/base/imgLogo_g.png'))?>" width="111" height="18" alt="Alt text"></a> のリンクです。<br>
            <!-- /.messageText --></td>
        </tr>
        </tbody>
        <!-- /.markdownList --></table>
</article>

<script>
    $(document).on('click', function() {
        disable
    })
</script>
<!-- /.markdownListWrap --></body>
</html>