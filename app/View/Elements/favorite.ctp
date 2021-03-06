<?php
	/**
	 * favorite.ctp
	 * Let User manage favorite hospitals.
	 * Standalone implementation.
	 */
	$uid = uniqid();
?>
<?php $this->append('script'); ?>
<script>
//(function(){
// Get Data from Server
var urls = {
	getFavoriteGroups: '<?php echo h(Router::url('/FavoriteHospitals/GetFavoriteGroups.json')); ?>',
	addFavoriteGroup: '<?php echo h(Router::url('/FavoriteHospitals/AddFavoriteGroup.json')); ?>',
	renameFavoriteGroup: '<?php echo h(Router::url('/FavoriteHospitals/RenameFavoriteGroup.json')); ?>',
	addHospital: '<?php echo h(Router::url('/FavoriteHospitals/AddHospitalToFavoriteGroup.json')); ?>',
	removeHospital: '<?php echo h(Router::url('/FavoriteHospitals/RemoveHospitalFromFavoriteGroup.json')); ?>',
	
	detail: '<?php echo h(Router::url('/hosdetail')); ?>',
	compare: '<?php echo h(Router::url('/Compare')); ?>',
	lineChart: '<?php echo h(Router::url('/LineChart')); ?>',
	bubbleChart: '<?php echo h(Router::url('/BubbleChart')); ?>',
	position: '<?php echo h(Router::url('/Position')); ?>'
};
var wamId = null;
<?php if(isset($wamId)): ?>
	wamId = '<?php echo h($wamId); ?>';
<?php endif; ?>
var isPremiumUser = <?php echo $isPremiumUser? 'true': 'false'; ?>;



function Message(level, body){
	var s = this;
	s.level = level;
	s.body = body;
	
	s.FmLevel = ko.computed(function(){
		return 'alert-' + s.level;
	});
}

// Represents a Hospital
function Hospital(parent, data){
	var s = this;
	s.parent = parent;
	s.data = data;
	s.RemoveFromParent = function(){
		s.parent.Remove(s);
	}
	s.DetailUrl = ko.computed(function(){
		return urls.detail + '/' + s.data.wam_id;
	});
	s.CompareUrl = ko.computed(function(){
		return urls.compare + '/' + s.data.wam_id;
	});
	s.PositionUrl = ko.computed(function(){
		return urls.position + '/' + s.data.wam_id;
	});
}

// Represents a FavoriteGroup (a set of hospitals)
function FavoriteGroup(parent, data){
	var s = this;
	s.parent = parent;
	s.id = data.FavoriteHospital.id;
	s.name = data.FavoriteHospital.name;
	var hospitals = [];
	for(var n=0; n<data.Hospital.length; n++){
		var h = data.Hospital[n];
		hospitals.push(new Hospital(s, h));
	}
	s.hospitals = hospitals;
	
	s.HasWamId = function(wamId){
		for(var n=0; n<s.hospitals.length; n++){
			var h = s.hospitals[n];
			if(h.data.wam_id == wamId)
				return true;
		}
		return false;
	}
	s.Remove = function(hospital){
		s.parent.RemoveHospital(s, hospital);
	}
}

// knockout.js AppModel
function AppModel(){
	var s = this;
	
	s.wamId = wamId;														// 閲覧中の医療機関のID
	s.loggedIn = ko.observable(false);					// ログイン中か
	s.username = ko.observable('');							// ユーザー名(ログイン中)
	s.favoriteGroups = ko.observableArray();		// 取得したお気に入りグループの一覧
	s.selectedFavoriteGroup = ko.observable();	// 選択中のお気に入りグループ
	s.newName = ko.observable('');							// 新しいグループ名
	s.messages = ko.observableArray([]);				// 画面に表示されているメッセージの一覧
	s.isPremium = isPremiumUser;								// プレミアムユーザーか
	
	s.LineChartUrl = ko.computed(function(){
		var g = s.selectedFavoriteGroup();
		if(typeof g == 'undefined')
			return null;
		return urls.lineChart + '/' + g.id;
	});
	
	s.BubbleChartUrl = ko.computed(function(){
		var g = s.selectedFavoriteGroup();
		if(typeof g == 'undefined')
			return null;
		return urls.bubbleChart + '/' + g.id;
	});
	
	// 画面にメッセージを表示する
	s.ShowMessage = function(message){
		console.info(message);
		s.messages.push(new Message('warning', message));
	}
	s.ShowSuccess = function(message){
		s.messages.push(new Message('success', message));
	}
	s.ShowWarn = function(message){
		s.messages.push(new Message('warning', message));
	}
	s.ShowError = function(message){
		s.messages.push(new Message('danger', message));
	}
	
	// お気に入りグループ一覧を(再)取得する
	s.GetFavoriteGroups = function(defaultGroupId){
		var selectedId = null;
		var group = s.selectedFavoriteGroup();
		if(group)
			selectedId = group.id;
		if(defaultGroupId !== undefined)
			selectedId = defaultGroupId;
		$.postJSON({
			url: urls.getFavoriteGroups
		}).done(function(data){
			s.loggedIn(data.dat.loggedIn);
			s.username(data.dat.username);
			var groups = [];
			for(var n=0; n<data.dat.favoriteGroups.length; n++){
				var g = data.dat.favoriteGroups[n];
				groups.push(new FavoriteGroup(s, g));
			}
			s.favoriteGroups(groups);
			for(var n=0; n<s.favoriteGroups().length; n++){
				var g = s.favoriteGroups()[n];
				if(g.id == selectedId){
					s.selectedFavoriteGroup(g);
					break;
				}
			}
		});
	}
	
	// お気に入りグループを追加する
	s.Add = function(){
		var newName = s.newName();
		s.newName('');
		$.postJSON({
			url: urls.addFavoriteGroup,
			data: {
				newName: newName
			}
		}).done(function(data){
			s.GetFavoriteGroups(data.dat.groupId);
			if(data.dat.result == true)
				s.ShowSuccess('お気に入りグループを追加しました。');
			else
				s.ShowError('お気に入りグループの追加に失敗しました。');
		}).fail(function(){
			s.ShowError('お気に入りグループの追加に失敗しました。');
		});
	}
	
	// 選択中のグループの名称を変更する
	s.Rename = function(){
		var group = s.selectedFavoriteGroup();
		var newName = s.newName();
		s.newName('');
		$.postJSON({
			url: urls.renameFavoriteGroup,
			data: {
				groupId: group.id,
				newName: newName
			}
		}).done(function(data){
			s.GetFavoriteGroups();
			if(data.dat.result == true)
				s.ShowSuccess('名称を変更しました。');
			else
				s.ShowError('名称の変更に失敗しました。');
		}).fail(function(){
			s.ShowError('名称の変更に失敗しました。');
		});
	}
	
	// 選択中のグループへ病院を追加する
	s.AddHospital = function(){
		if(s.wamId==null){
			s.ShowMessage('wamId is null.');
			return;
		}
		
		var group = s.selectedFavoriteGroup();
		if(group.HasWamId(s.wamId)){
			s.ShowWarn('既に登録されています。');
			return;
		}
		
		$.postJSON({
			url: urls.addHospital,
			data: {
				groupId: group.id,
				wamId: s.wamId
			}
		}).done(function(data){
			s.GetFavoriteGroups();
			if(data.dat.result == true)
				s.ShowSuccess('登録しました。');
			else
				s.ShowError('登録に失敗しました。');
		}).fail(function(){
			s.ShowError('登録に失敗しました。');
		});
	}
	
	// 選択中のグループから病院を削除する
	s.RemoveHospital = function(group, hospital){
		$.postJSON({
			url: urls.removeHospital,
			data: {
				groupId: group.id,
				wamId: hospital.data.wam_id
			}
		}).done(function(data){
			s.GetFavoriteGroups();
			if(data.dat.result == true)
				s.ShowSuccess('登録を解除しました。');
			else
				s.ShowError('登録の解除に失敗しました。');
		}).fail(function(){
			s.ShowError('登録の解除に失敗しました。');
		});
	}
	
	// 最初に読み込む
	s.GetFavoriteGroups();
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('<?php echo h($uid); ?>'));
//})();
</script>
<?php $this->end(); ?>



<!-- お気に入り管理(ログインしている場合) -->
<div id="<?php echo h($uid); ?>" class="favorite">
	<div data-bind="text: name"></div>
	<?php if(empty($compact)): ?>
	<div class="box">
		<h2>
			<?php echo $this->Html->image('icon/h2.png', array('style'=>'padding-bottom:2px;')); ?>
			お気に入り病院グループ
		</h2>
	<?php else: ?>
	<div>
	<?php endif; ?>
		
		<!-- ログインしている場合 -->
		<div data-bind="if: loggedIn(), visible: loggedIn()" class="content">
			
			<!-- メッセージ -->
			<div data-bind="foreach: messages" class="messages">
				<div data-bind="attr:{class: 'alert alert-dismissable ' + FmLevel()}">
				  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				  <span data-bind="text: body"></span>
				</div>
			</div>
			
			<table>
				<tr>
					<td valign="top">
						<!-- グループ選択 -->
						<select data-bind="visible: favoriteGroups().length > 0, options: favoriteGroups, optionsText: 'name', value: selectedFavoriteGroup" class="form-control"></select>
					</td>
					<?php if(empty($compact)): ?>
			<!-- ヘルプ -->
			<p><ul class="basic">
			<li class="li2">1. まず最初に、「操作」→「お気に入りグループを作成する」で、お気に入りグループを作成してください。</li>
			<li class="li2">2. 各病院ページで、病院名の下に表示されるメニューで「登録したいグループ名」を選択してください。</li>
			<li class="li2">3. 「この病院をお気に入りに登録」ボタンを押すと、グループへの登録が完了します。</li>
			</ul></p>
					<td>
						<!-- グループ操作 -->
						<div class="btn-group">
						  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						    操作 <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" role="menu">
						    <li><a data-bind="visible: favoriteGroups().length < 10" data-toggle="modal" onclick="$('#AddGroupModal').modal('show');">お気に入りグループを作成する</a></li>
						    <li><a data-bind="visible: selectedFavoriteGroup()" data-toggle="modal" onclick="$('#RenameGroupModal').modal('show');">グループ名を変更する</a></li>
						    <li data-bind="visible: selectedFavoriteGroup()? (selectedFavoriteGroup().hospitals.length < 15 && wamId): false"><a data-bind="click: AddHospital">閲覧中の病院を登録する</a></li>
						    <li><a data-bind="visible: selectedFavoriteGroup() && isPremium, attr:{href:LineChartUrl}">グループ内時系列分析（折れ線グラフ）</a></li>
						    <li><a data-bind="visible: selectedFavoriteGroup() && isPremium, attr:{href:BubbleChartUrl}">グループ内ポジション分析（バブルチャート）</a></li>
						  </ul>
						</div>
					</td>
				</tr>
			</table>
					<?php else: ?>
					<!-- リストに追加(コンパクト版) -->
					<td>
						<div data-bind="if: selectedFavoriteGroup() != null"><button type="button" class="btn btn-default" data-bind="click: AddHospital">この病院をお気に入りに登録</button></div>
						<div data-bind="if: selectedFavoriteGroup() == null"><?php echo $this->Html->link('お気に入り管理からリストを作成して下さい。', '/Users/Subscribe'); ?></div>
					</td>
				</tr>
			</table>
			<p class="muted">「登録したいグループ名」を選択した上で、「この病院をお気に入りに登録」ボタンを押してください。<br/>（※新しいグループは、<a href="http://hospia.jp/Users/Subscribe">「お気に入り管理画面」</a>で作成できます。）</p>
					<?php endif; ?>
			
			<!-- モーダル(新しいグループ) -->
			<div class="modal fade" id="AddGroupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title" id="myModalLabel">お気に入りグループを作成する</h4>
			      </div>
			      <div class="modal-body">
							<input type="text" data-bind="value: newName" class="form-control" placeholder="新しいグループ名" maxlength="12" />
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			        <button type="button" data-bind="click: Add" class="btn btn-primary" data-dismiss="modal">作成する</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			<!-- モーダル(名称変更) -->
			<div class="modal fade" id="RenameGroupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title" id="myModalLabel">グループ名を変更する</h4>
			      </div>
			      <div class="modal-body">
			      	<span data-bind="if: selectedFavoriteGroup()">現在のグループ名：<span data-bind="text: selectedFavoriteGroup().name"></span></span>
							<input type="text" data-bind="value: newName" class="form-control" placeholder="新しいグループ名" maxlength="12" />
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			        <button type="button" data-bind="click: Rename" class="btn btn-primary" data-dismiss="modal">保存する</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			<?php if(empty($compact)): ?>
			<!-- このグループの病院 -->
			<div data-bind="with: selectedFavoriteGroup()">
				<ul data-bind="foreach: hospitals" class="items-half">
					<li>
						<table>
							<tr>
								<td><a data-bind="text: data.name, attr:{href:DetailUrl}"></a></td>
								<td data-bind="visible: $root.isPremium" style="width: 80px;"><a data-bind="attr:{href:CompareUrl}">時系列分析</a></td>
								<td data-bind="visible: $root.isPremium" style="width: 90px;"><a data-bind="attr:{href:PositionUrl}">ポジション分析</a></td>
								<td style="width: 80px;"><button type="button" data-bind="click: RemoveFromParent">解除する</button></td>
							</tr>
						</table>
					</li>
				</ul>
				<p><div data-bind="visible: hospitals.length == 0">登録されている病院はありません。</div></p>
				・このグループには<span data-bind="text: hospitals.length"></span>病院登録されています。
				(あと<span data-bind="text: 15-hospitals.length"></span>病院登録可)<br/>
				・<span data-bind="text: $root.favoriteGroups().length"></span>グループ登録されています。
				(あと<span data-bind="text: 10-$root.favoriteGroups().length"></span>グループ登録可)
			</div>
			
			<!-- プレミアム会員権管理(ログインしていてプレミアム会員である場合) -->
			<p data-bind="visible: isPremium">
				<?php echo $this->Html->link('プレミアム会員権管理', array('controller'=>'Users', 'action'=>'Subscribe')); ?>
			</p>
			
			<!-- 課金を促すメッセージ(ログインしていてプレミアムでない場合) -->
			<p data-bind="visible: !isPremium">
				<?php echo $this->Html->link('・月額1,000円でプレミアム機能をご利用いただけます。お支払いはいつでも停止が可能です。', array('controller'=>'Users', 'action'=>'Subscribe')); ?>
			</p>
			
			<?php endif; ?>
			
		</div>
		
		<?php if(empty($compact)): ?>
		<!-- ログインを促すメッセージ(ログインしていない場合) -->
		<div data-bind="if: !loggedIn()" class="content">
			<?php echo $this->Html->link('無料会員登録をしていただくと、お気に入りグループ登録などの機能をご利用いただけます。', array('controller'=>'Users', 'action'=>'Login')); ?>
		</div>
		<?php endif; ?>
	</div>
</div>