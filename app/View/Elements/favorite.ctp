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
(function(){
// Get Data from Server
var urls = {
	getFavoriteGroups: '<?php echo h(Router::url('/FavoriteHospitals/GetFavoriteGroups.json')); ?>',
	addFavoriteGroup: '<?php echo h(Router::url('/FavoriteHospitals/AddFavoriteGroup.json')); ?>',
	renameFavoriteGroup: '<?php echo h(Router::url('/FavoriteHospitals/RenameFavoriteGroup.json')); ?>',
	addHospital: '<?php echo h(Router::url('/FavoriteHospitals/AddHospitalToFavoriteGroup.json')); ?>',
	removeHospital: '<?php echo h(Router::url('/FavoriteHospitals/RemoveHospitalFromFavoriteGroup.json')); ?>'
};
var wamId = null;
<?php if(isset($wamId)): ?>
	wamId = '<?php echo h($wamId); ?>';
<?php endif; ?>



// Represents a Hospital
function Hospital(parent, data){
	var s = this;
	s.parent = parent;
	s.data = data;
	s.RemoveFromParent = function(){
		s.parent.Remove(s);
	}
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
	s.favoriteGroups = ko.observableArray();		// 取得したお気に入りグループの一覧
	s.selectedFavoriteGroup = ko.observable();	// 選択中のお気に入りグループ
	s.newName = ko.observable('');							// 新しいグループ名
	
	s.selectedFavoriteGroup.subscribe(function(newValue){
		s.newName(newValue.name);
	});
	
	// 画面にメッセージを表示する
	s.ShowMessage = function(message){
		console.info(message);
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
		$.postJSON({
			url: urls.addFavoriteGroup,
			data: {
				newName: newName
			}
		}).done(function(data){
			s.GetFavoriteGroups(data.dat.groupId);
		});
	}
	
	// 選択中のグループの名称を変更する
	s.Rename = function(){
		var group = s.selectedFavoriteGroup();
		var newName = s.newName();
		$.postJSON({
			url: urls.renameFavoriteGroup,
			data: {
				groupId: group.id,
				newName: newName
			}
		}).done(function(data){
			s.GetFavoriteGroups();
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
			s.ShowMessage('Already added to this group.');
			return;
		}
		
		s.ShowMessage('Adding...');
		$.postJSON({
			url: urls.addHospital,
			data: {
				groupId: group.id,
				wamId: s.wamId
			}
		}).done(function(data){
			s.GetFavoriteGroups();
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
		});
	}
	
	// 最初に読み込む
	s.GetFavoriteGroups();
}

var model = new AppModel();
ko.applyBindings(model, document.getElementById('<?php echo h($uid); ?>'));
})();
</script>
<?php $this->end(); ?>



<div id="<?php echo h($uid); ?>">
	<!-- お気に入りグループ一覧(ログインしている場合) -->
	<div data-bind="if: loggedIn()">
		<div data-bind="text: wamId"></div>
		<select data-bind="options: favoriteGroups, optionsText: 'name', value: selectedFavoriteGroup"></select>
		<div data-bind="if: selectedFavoriteGroup()">
			<ul data-bind="foreach: selectedFavoriteGroup().hospitals">
				<li>
					<span data-bind="text: data.wam_id"></span>
					<span data-bind="text: data.name"></span>
					<button type="button" data-bind="click: RemoveFromParent">Remove</button>
				</li>
			</ul>
			<button type="button" data-bind="click: Rename">Rename</button>
			<button type="button" data-bind="click: AddHospital">AddHospital</button>
		</div>
		<input type="text" data-bind="value: newName" />
		<button type="button" data-bind="click: Add">Add</button>
	</div>
	<!-- ログインを促すメッセージ(ログインしていない場合) -->
	<div data-bind="if: !loggedIn()">
		この病院をお気に入りに登録するにはログインして下さい。
	</div>
</div>
