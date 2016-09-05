
<section class="moduleCont1">
    <h1 class="editDesign1 jsModuleContTile">デザイン</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <ul class="moduleSetting">
            <?php foreach ($data['design_type_arr'] as $key => $type): ?>
                <li>
                    <?php write_html( $this->formRadio( 'design_type', PHPParser::ACTION_FORM, array( $data['disable'] => $data['disable']), array($key => $type), array(), " ")); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>