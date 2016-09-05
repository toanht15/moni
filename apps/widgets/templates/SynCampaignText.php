<?php if( $data['isForSyndotOnly'] ): ?>
    <section class="campaignText">
        <div class="synLotCampaign">
            <h1><img src="<?php assign($this->setVersion('/img/campaign/synLotTitle01.png')) ?>" alt="続々当選のチャンス!"></h1>

            <div class="synLotCounter">
                <h2><img src="<?php assign($this->setVersion('/img/campaign/synLotTitle02.png')) ?>" alt="現在までの当選数">
                </h2>
                <p><?php assign($data['electedCount']) ?><span>人</span></p>
                <!-- /.synLotCounter --></div>

            <h1><img src="<?php assign($this->setVersion('/img/campaign/synLotTitle03.png')) ?>" alt="毎日おトクなキャンペーン"
                     href="#"></h1>
            <ul class="synLotPointList">
                <li><img src="<?php assign($this->setVersion('/img/campaign/synLotPint01.png')) ?>"
                         alt="POINT1 その場で当たりがわかる!"></li>
                <li><img src="<?php assign($this->setVersion('/img/campaign/synLotPint02.png')) ?>"
                         alt="POINT2 連続参加で当選確率2倍"></li>
                <li><img src="<?php assign($this->setVersion('/img/campaign/synLotPint03.png')) ?>"
                         alt="POINT3 Wチャンス！他のサービスを楽しんでもう1回チャレンジ！"></li>
            </ul>
            <p>1日1回チャレンジできる！その場であたりがわかるスピードくじに参加して賞品をGETしよう！</p>
            <!-- /.synLot --></div>
    </section>
<?php endif; ?>