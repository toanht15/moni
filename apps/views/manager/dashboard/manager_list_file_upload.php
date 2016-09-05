
<?php foreach ($data['images'] as $img):?>
    <?php $imgsrc = StorageClient::getInstance()->getObjectUrl($img['Key']); ?>
    <a href="" onclick="getUrl('<?php assign($data['cback']) ?>', '<?php assign($imgsrc) ?>')">
        <img src="<?php assign($imgsrc) ?>" style="width: 200px; height: 200px;"/></a>
<?php endforeach;?>
    <script type="text/javascript">
        function getUrl(cback, imgfilepath)
        {
            window.opener.CKEDITOR.tools.callFunction(cback, imgfilepath);
            window.close();
        }
    </script>
