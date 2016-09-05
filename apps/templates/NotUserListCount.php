<?php if($data['total_count'] && !$data['all_not_sent_user_count']):?>
    <p>すべてのユーザーに送信済です。</p>
<?php elseif(!$data['page_not_sent_user_count'] && $data['all_not_sent_user_count']): //ページ内に未送信の人がいない && 2ページ目以降に未送信の人がいる?>
    <p>このページ内のユーザーはすべて送信済です。<a href="javascript:void(0)" data-select="select_all_users">未送信のユーザー <strong class="num"><?php assign(number_format($data['all_not_sent_user_count'])) ?></strong> 人をすべて選択</a></p>
<?php elseif($data['page_not_sent_user_count'] && $data['all_not_sent_user_count'] == $data['page_not_sent_user_count'])://未送信のユーザがページ内にしかいない ?>
    <p>このページ内の未送信のユーザー <strong class="num"><?php assign($data['page_not_sent_user_count']) ?></strong> 人すべてが選択されています。</p>
<?php else://未送信のユーザがページ内にいて、ページ外にもいる?>
    <p>このページ内の未送信のユーザー <strong class="num" data-user_type="page_user"><?php assign($data['page_not_sent_user_count']) ?></strong> 人すべてが選択されています。<a href="javascript:void(0)" data-select="select_all_users">未送信のユーザー <strong class="num"><?php assign(number_format($data['all_not_sent_user_count'])) ?></strong> 人をすべて選択</a></p>
<?php endif; ?>
<p style="display: none">すべての未送信のユーザー <strong class="num"><?php assign($data['all_not_sent_user_count']) ?></strong> 人が選択されています。<a href="javascript:void(0)" data-select="deselect_all_users">選択解除</a></p>
