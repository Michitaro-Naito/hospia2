<h1>hospia.jp管理用ページ</h1>

<table class="table table-striped">
	<thead>
		<tr>
			<th>機能</th>
			<th>詳細</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $this->Html->link('ユーザー管理', '/Users'); ?></td>
			<td>hospia.jpに登録されている全てのユーザー(管理者含む)を検索追加編集できます。</td>
		</tr>
		<tr>
			<td><?php echo $this->Html->link('記事管理', '/Post'); ?></td>
			<td>ユーザーに表示される記事を検索追加編集できます。</td>
		</tr>
		<tr>
			<td><?php echo $this->Html->link('バルーンチップ管理', '/Tip'); ?></td>
			<td>ユーザーに表示されるバルーンチップ(吹き出しのヘルプ)を検索追加編集できます。</td>
		</tr>
		<tr>
			<td><?php echo $this->Html->link('距離再計算ツール', '/admin/distance'); ?></td>
			<td>CoordinatesテーブルからDistanceテーブルを生成するためのツールです。</td>
		</tr>
		<tr>
			<td><?php echo $this->Html->link('ユーザー集計ツール', '/Users/Statistics'); ?></td>
			<td>通常会員とプレミアム会員をそれぞれ数え上げるためのツールです。</td>
		</tr>
		<tr>
			<td><?php echo $this->Html->link('ログアウト', '/users/logout'); ?></td>
			<td>hospia.jpからログアウトします。</td>
		</tr>
	</tbody>
</table>
