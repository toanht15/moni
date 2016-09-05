<?php if($data['entry']):?>
	<section class="freeAreaWrap jsEditAreaWrap">
		<div class="freeArea">
			<div class="ckeditorWrap"><?php write_html($data['entry']->body)?></div>
        <?php if($data['isLoginAdmin']):?>
			<section class="editArea">
				<a href="#freeAreaEntries" class="jsOpenModal"><span>編集する</span></a>
			</section>
        <?php endif;?>
		</div>
	</section>
<?php elseif($data['isLoginAdmin'] && !$data['isAgent']):?>
	<section class="freeAreaWrap">
		<section class="addArea">
			<a href="#freeAreaEntries" class="jsOpenModal"><span>追加する</span></a>
		</section>
	</section>
<?php endif;?>