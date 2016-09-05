<html>
<head>
	<title><?php echo basename( $fn )?></title>
	<style>
		table, td {
			 border solid thin
		}
	</style>
</head>
<body>
	<h1><?php echo basename( $fn ) ?></h1>
	<h2><?php echo $data['name']?></h2>
	<h3>クラス情報</h3>
	<table>
	<?php if( $data['author'] ) :?>
	<tr>
		<th>作成者</th>
		<td><?php echo $data['author'][0]?></td>
	</tr>
	<?php endif; ?>
	<?php if( $data['date'] ) :?>
	<tr>
		<th>日付</th>
		<td><?php echo $data['date'][0]?></td>
	</tr>
	<? endif; ?>
	<?php if( $data['memo'] ) :?>
	<tr>
		<th>概要</th>
		<td><pre><?php echo $data['memo']?></pre></td>
	</tr>
	<?php endif;?>
	</table>
  <?php if( $data['methods'] ):?>
	<h2>メソッド</h2>
	<table>
	<tr>
		<th>名称</th>
		<th>引数</th>
		<th>概要</th>
	</tr>
	<?php foreach( $data['methods'] as $m ) :?>
	<tr>
		<td><?php echo $m['name']?></td>
		<td>
			<?php if( $dm['args'] ):?>
			<ul>
				<?php foreach( $m['args'] as $a ):?>
				<li><?php echo $a['name']?><?php echo $a['memo'] ? ' - ' . $a['memo'] : ''?></li>
				<?php endforeach; ?>
			</ul>
			<?php else:?>
				引数なし
			<?php endif;?>
		</td>
		<td><pre><?php echo $m['memo']?></pre></td>
	</tr>
	<?php endforeach; ?>
	</table>
  <?php endif; ?>
</body>
</html>
