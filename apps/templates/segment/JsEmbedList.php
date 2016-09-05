<script id="adding_new_spc_template" type="text/html">
    <li class="metricBoxAddWrap jsSPCComponent">
        <p class="metricBoxAdd"><span><a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal">追加する</a></span></p>
        <!-- /.metricBoxAddWrap --></li>
</script>

<script id="adding_spc_template" type="text/html">
    <li class="metricBoxAddWrap jsSPCComponent">
        <p class="metricBoxAdd"><span><a href="#segmentProvisionConditionSelector" data-type="and" data-action_type="" class="jsOpenSegmentConditionModal">追加する</a></span></p>
        <!-- /.metricBoxAddWrap --></li>
</script>

<script id="blank_spc_template" type="text/html">
    <li class="metricBoxOptionAdd jsSPCComponent">
        <input type="hidden" name="spc" value="" class="jsSPCComponentValue">
        <a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal"><span>追加する</span></a>
        <!-- /.metricBoxOptionAdd --></li>
</script>

<script id="loading_img" type="text/html">
    <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg">
</script>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css')) ?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>