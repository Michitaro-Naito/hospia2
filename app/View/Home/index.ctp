<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;
App::uses('Debugger', 'Utility');
?>
<h2><?php echo __d('cake_dev', 'Release Notes for CakePHP %s.', Configure::version()); ?></h2>
<p>
	<a href="http://cakephp.org/changelogs/<?php echo Configure::version(); ?>"><?php echo __d('cake_dev', 'Read the changelog'); ?> </a>
</p>
<?php
if (Configure::read('debug') > 0):
	Debugger::checkSecurityKeys();
endif;
?>
<p id="url-rewriting-warning" style="background-color:#e32; color:#fff;">
	<?php echo __d('cake_dev', 'URL rewriting is not properly configured on your server.'); ?>
	1) <a target="_blank" href="http://book.cakephp.org/2.0/en/installation/url-rewriting.html" style="color:#fff;">Help me configure it</a>
	2) <a target="_blank" href="http://book.cakephp.org/2.0/en/development/configuration.html#cakephp-core-configuration" style="color:#fff;">I don't / can't use URL rewriting</a>
</p>
<p>
<?php
	if (version_compare(PHP_VERSION, '5.2.8', '>=')):
		echo '<span class="notice success">';
			echo __d('cake_dev', 'Your version of PHP is 5.2.8 or higher.');
		echo '</span>';
	else:
		echo '<span class="notice">';
			echo __d('cake_dev', 'Your version of PHP is too low. You need PHP 5.2.8 or higher to use CakePHP.');
		echo '</span>';
	endif;
?>
</p>
<p>
	<?php
		if (is_writable(TMP)):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'Your tmp directory is writable.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your tmp directory is NOT writable.');
			echo '</span>';
		endif;
	?>
</p>
<p>
	<?php
		$settings = Cache::settings();
		if (!empty($settings)):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'The %s is being used for core caching. To change the config edit APP/Config/core.php ', '<em>'. $settings['engine'] . 'Engine</em>');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your cache is NOT working. Please check the settings in APP/Config/core.php');
			echo '</span>';
		endif;
	?>
</p>
<p>
	<?php
		$filePresent = null;
		if (file_exists(APP . 'Config' . DS . 'database.php')):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'Your database configuration file is present.');
				$filePresent = true;
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your database configuration file is NOT present.');
				echo '<br/>';
				echo __d('cake_dev', 'Rename APP/Config/database.php.default to APP/Config/database.php');
			echo '</span>';
		endif;
	?>
</p>
<?php
if (isset($filePresent)):
	App::uses('ConnectionManager', 'Model');
	try {
		$connected = ConnectionManager::getDataSource('default');
	} catch (Exception $connectionError) {
		$connected = false;
		$errorMsg = $connectionError->getMessage();
		if (method_exists($connectionError, 'getAttributes')):
			$attributes = $connectionError->getAttributes();
			if (isset($errorMsg['message'])):
				$errorMsg .= '<br />' . $attributes['message'];
			endif;
		endif;
	}
?>
<p>
	<?php
		if ($connected && $connected->isConnected()):
			echo '<span class="notice success">';
	 			echo __d('cake_dev', 'Cake is able to connect to the database.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Cake is NOT able to connect to the database.');
				echo '<br /><br />';
				echo $errorMsg;
			echo '</span>';
		endif;
	?>
</p>
<?php endif; ?>
<?php
	App::uses('Validation', 'Utility');
	if (!Validation::alphaNumeric('cakephp')):
		echo '<p><span class="notice">';
			echo __d('cake_dev', 'PCRE has not been compiled with Unicode support.');
			echo '<br/>';
			echo __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring');
		echo '</span></p>';
	endif;
?>

<p>
	<?php
		if (CakePlugin::loaded('DebugKit')):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'DebugKit plugin is present');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'DebugKit is not installed. It will help you inspect and debug different aspects of your application.');
				echo '<br/>';
				echo __d('cake_dev', 'You can install it from %s', $this->Html->link('github', 'https://github.com/cakephp/debug_kit'));
			echo '</span>';
		endif;
	?>
</p>



<?php
	$this->set('is_top', TRUE);
?>
<?php $this->start('script'); ?>
<script>
// Get initial variables from server
var dat = JSON.parse('<?php echo json_encode($dat); ?>');
console.info(dat);

function AppModel(){
	var s = this;
	
	s.prefectures = dat.prefectures;
	s.zones = ko.observableArray();
	s.hospitalName = ko.observable();
	
	s.selectedPrefecture = ko.observable();
	s.selectedZone = ko.observable();
	
	// 選択された都道府県に合わせて医療圏を再読み込み
	s.selectedPrefecture.subscribe(function(newValue){
		if(newValue.id !== null){
			$.postJSON({
				url: dat.getZonesUrl,
				data: {
					prefectureId: newValue.id
				}
			}).done(function(data){
				s.zones(data.zones);
			});
		}
	});
	
	// hoslstへページ移動する。その際、選択された都道府県・医療圏・病院名を渡す。
	s.GotoHoslst = function(){
		var uri = new Uri(dat.hoslistUrl);
		uri.replaceQueryParam('prefectureId', s.selectedPrefecture().id);
		if(s.selectedZone()) uri.replaceQueryParam('zoneId', s.selectedZone().id);
		uri.replaceQueryParam('hospitalName', s.hospitalName());
		window.location.href = uri.toString();
	}
}

var model = new AppModel();
ko.applyBindings(model);
</script>
<?php $this->end(); ?>



<?php echo $this->element('fb_root'); ?>
<div class="row">
	<div class="col-sm-6">
		<select data-bind="options: prefectures, optionsText: 'name', value: selectedPrefecture"></select>
		医療圏<?php echo $this->My->tip('医療圏'); ?>：
		<select data-bind="options: zones, optionsText: 'name', value: selectedZone"></select>
		<input type="text" data-bind="value: hospitalName"/>
		<button type="button" data-bind="click: GotoHoslst">検索</button>
	</div>
	<div class="col-sm-6">ご利用ガイド</div>
</div>

<div class="row">
	<div class="col-sm-9">
		<div class="row">
			<div class="col-sm-12">新着情報</div>
			<?php foreach($recentPosts as $p): ?>
				<div><?php echo $this->Html->link($p['Post']['title'], "/wp/archives/{$p['Post']['post_id']}"); ?></div>
			<?php endforeach; ?>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<?php
					echo $this->element('twitter_follow');
					echo $this->element('fb_follow');
				?>
			</div>
		</div>
		<div class="row">
  		<div class="col-sm-6">
  			最近チェックした病院
  			<ul>
	  			<?php foreach($dat['rememberedHospitals'] as $h): ?>
	  				<li><?php echo h($h['Hospital']['name']); ?></li>
	  			<?php endforeach; ?>
  			</ul>
  		</div>
  		<div class="col-sm-6">
  			閲覧数の多い病院
  			<ul>
  				<?php foreach($dat['hospitalsMostViewed'] as $h): ?>
  					<li><?php echo h($h['Hospital']['name'] . $h[0]['sum']); ?></li>
  				<?php endforeach; ?>
  			</ul>
  		</div>
		</div>
		<div class="row">
  		<div class="col-sm-12">お気に入りグループ一覧</div>
		</div>
	</div>
	<div class="col-sm-3 bs-sidebar">
		<div class="row">
  		<div class="col-sm-12">
  			疾患別
  			<ul>
  				<?php foreach($dat['maladyCategories'] as $key => $c): ?>
  					<li><?php echo $this->Html->link($c['name'], array('action'=>'Maladylist', 'controller'=>'Home', '?'=>array('mdata'=>$c['id']))); ?></li>
  				<?php endforeach; ?>
  			</ul>
  		</div>
		</div>
		<div class="row">
  		<div class="col-sm-12">
  			診断分類別
  			<ul>
  				<?php foreach($dat['mdcs'] as $key => $m): ?>
  					<li><?php echo $this->Html->link('MDC'.str_pad($key, 2, '0', STR_PAD_LEFT).' '.$m['name'], array('action'=>'Toplst', 'controller'=>'Home', '?'=>array('id'=>$m['id']))); ?></li>
  				<?php endforeach; ?>
  			</ul>
  		</div>
		</div>
		<?php echo $this->element('ad_sidebar'); ?>
	</div>
</div>
