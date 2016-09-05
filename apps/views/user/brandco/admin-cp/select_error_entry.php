<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'])))?>
<article class="modalInner-large">
    <header class="innerIG">
        <h1>パネル管理</h1>
    </header>
    <section class="modalInner-cont">
        アカウントが選択されていません。
    </section>
    <footer>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModalFrame" data-type="refreshTop">戻る</a></span>
        </p>
    </footer>
</article>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('')))) ?>

