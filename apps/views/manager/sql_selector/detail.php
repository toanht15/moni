<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'データ抽出',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl( 'sql_selector', 'index', array(), array(), '', true )); ?>">データ抽出</a></li>
                <li class="active"><?php assign($this->sqlSelector->title); ?></li>
            </ol>
            <h1 class="sub-header"><?php assign($this->sqlSelector->title); ?></h1>

            <form name="sql_search" action="<?php assign(Util::rewriteUrl('sql_selector', 'detail',array($this->sqlId), array(), '', true)); ?>" method="GET" class="form-horizontal row-border">
            <?php if( $this->check == SqlSelectorService::CHECK_CONVERSION ): ?>
            <h4><span class="edit"><a href="#" class="jsMessageSetting">詳細検索条件</a></span></h4>
                    <div class="jsMessageSettingTarget" style="display:1">
                        <div class="container">
                            <div class="col-md-11 col-sm-6 col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <label class="col-md-2 control-label">ブランドID</label>
                                        <div class="col-md-10">
                                            <label class="checkbox-inline">
                                                <?php write_html( $this->formText(
                                                    'brand_id',
                                                    PHPParser::ACTION_FORM,
                                                    array('maxlength'=>'255', 'placeholder'=> 'ブランドID')
                                                )); ?>
                                            </label>
                                            <a href="javascript:void(0);" onclick="document.sql_search.submit();return false;" class="btn btn-primary btn-xs">検索</a><br>
                                        </div>
                                        <?php if($this->conversion):?>
                                        <div>検索結果</div>
                                        <div class="col-md-3 input-group">
                                            <span class="input-group-addon" id="basic-addon3">ブラント名</span>
                                            <input type="text" class="form-control" id="basic-url" value="<?php assign($this->brand_name ); ?>" placeholder=" ">
                                        </div>
                                        <div class="col-md-10 input-group" id="inputs">
                                            <span class="input-group-addon" id="basic-addon3">コンバージョン名</span>
                                            <?php foreach( $this->conversion as $key => $value ): ?>
                                                &nbsp
                                                    <?php write_html($this->formCheckBox( 'conversion_id', PHPParser::ACTION_FORM,
                                                        array() ,array($key=>$value))); ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="col-md-3 input-group" id="inputs">
                                        <span class="input-group-addon" id="basic-addon3">コンバージョンID</span>
                                        <input type="text" id="target"/>
                                        </div>
                                        <?php else: ?>
                                            検索結果がありません
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endif;?>

                    <h4><?php write_html($this->nl2brAndHtmlspecialchars($this->sqlSelector->description)); ?></h4>

                    <?php $no = '0'; ?>
                    <?php foreach( $this->controllers as $item ): ?>
                <div class="form-group">
                            <?php assign( $item["title"] ); ?>
                        <?php if($item["required"]):?><small style="color:#d00">*</small><?php endif;?>
                                <?php if( $item["type"] == 'date' ): ?>
                                    <?php write_html( $this->formText( 'search'.$no, $this->GET['search'.$no], array( 'id' => 'search'.$no, 'class' => 'jsDate inputDate'))); ?>
                                <?php elseif( $item["type"] == 'string' ): ?>
                                    <?php write_html( $this->formText( 'search'.$no, $this->GET['search'.$no], array( 'id' => 'search'.$no))); ?>
                                <?php endif;?>
                                <?php write_html( $this->showError( 'search'.$no, 'errors' )); ?>
                        <?php $no++;?>
                </div>
                    <?php endforeach; ?>

                <input type='hidden' name="mode" id="mode" value=''/>
                <div class="btn-group">
                    <button type="button" name="search" class="btn btn-primary btn-large" onClick='$("#mode").val("display");this.form.submit();$("#mode").val("");'>　検索　</button>
                    <button type="button" name="search" class="btn btn-primary btn-large" onClick='$("#mode").val("csv");this.form.submit();$("#mode").val("");'>CSVダウンロード</button>
                </div>
            </form>
            <?php foreach ($this->rs as $key => $rs): ?>
                <?php if( $this->columns[$key] ): ?>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <?php foreach( $this->columns[$key] as $item ): ?>
                                <th><?php assign(  $item);?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $cnt = 0;?>
                        <?php while( $row = $this->db->fetch( $rs )): ?>
                            <tr>
                                <?php foreach( $row as $key => $val ): ?>
                                    <?php if( strpos( $key, "(serialize)" ) !== false ){
                                        echo '<td>';
                                        if( $val ){
                                            $list = unserialize($val);
                                            if( count( $list ) > 0 ){
                                                foreach($list as $key => $val){
                                                    echo $key . "=" . $val . "<br />";
                                                }
                                            }
                                        }
                                        echo '</td>';
                                    } else{ ?>
                                        <td><?php assign( $val ); ?></td>
                                    <?php } ?>
                                <?php endforeach;?>
                            </tr>
                            <?php if( $cnt++ > 1000 ) break; ?>
                        <?php endwhile; ?>

                    </table>

                    <?php if( $cnt > 1000 ): ?>
                        <div class="alert alert-warning">件数が1000件を超えたので表示を停止しました。CSVをダウンロードして閲覧してください。</div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-warning" style="margin-top: 10px;">検索結果が0件です。</div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    </div><!-- row -->
</div><!-- container-fluid -->

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<script>
    $(".jsDate").datepicker({
        minDate: null
    });
</script>

<script>
    var arr = [];
    $('#inputs input').change(function() {
        if (this.checked) {
            arr.push(this.value);
        }
        else {
            arr.splice(arr.indexOf(this.value), 1);
        }
        $('#target').val(arr + '');
    });
</script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>

