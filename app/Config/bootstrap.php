<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

//Soft Delete
CakePlugin::load('CakeSoftDelete');

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));



/**
 * 定数
 */

// 表示切替：基本情報
Configure::write('basic', array(
	'bed' => '全病床数',
	'general' => '一般病床数',
	'doctor' => '医師数',
	'nurse' => '看護師数'
));

//　表示切替：症例数
Configure::write('mdc', array(
	'全症例数',
	'神経系',
	'眼科系',
	'耳鼻咽喉科系',
	'呼吸器系',
	'循環器系',
	'消化器系',
	'筋骨格系',
	'皮膚系',
	'乳房系',
	'内分泌系',
	'腎・尿路系',
	'女性生殖器系',
	'血液系',
	'新生児系',
	'小児系',
	'外傷系',
	'精神系',
	'その他'
));

// 表示切替：DPC
Configure::write('dpc', array(
	'ave_month'		=> '月平均患者数',
	'zone_share'	=> '医療圏シェア',
	'ave_in'			=> '平均在院日数',
	'complex'			=> '患者構成指標',
	'efficiency'	=> '在院日数指標'
));

// 表示切替：比較区分
Configure::write('ctgry', array(
	'dpc'		=> '診療実績',
	'basic'	=> '基本情報'
));

// 表示切替：比較リスト
Configure::write('clst', array(
	'distance'	=> '距離が近い病院',
	'number'		=> '患者数が多い病院'
));

// 表示切替：Top List 比較指数
Configure::write('cmplst', array(
	'ave_month@dpc'			=> '月平均患者数',
	'zone_share@dpc'		=> '医療圏シェア',
	'ave_in@dpc'				=> '平均在院日数',
	'complex@dpc'				=> '患者構成指標',
	'efficiency@dpc'		=> '在院日数指標'
));

if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '192.168.100.2' || $_SERVER['SERVER_NAME'] == 'v157-7-208-129.myvps.jp'){
	// テスト用アカウント
	// GoogleWallet
	Configure::write('GoogleWallet_SellerId', '04806629248295947480');
	Configure::write('GoogleWallet_SellerSecret', 'xzFzun3WgEG6nAc1x0rtOQ');
	Configure::write('GoogleWallet_ScriptUrl', 'https://sandbox.google.com/checkout/inapp/lib/buy.js');
	
	// Facebook
	Configure::write('Facebook_AppId', '371990586279310');
	
}else{
	// 本番用アカウント
	// GoogleWallet
	Configure::write('GoogleWallet_SellerId', '08098458298793310506');
	Configure::write('GoogleWallet_SellerSecret', '_2JSZL20XpIyKO9n6XJw0A');
	Configure::write('GoogleWallet_ScriptUrl', 'https://wallet.google.com/inapp/lib/buy.js');
	
	// Facebook
	Configure::write('Facebook_AppId', '616515881759269');
}

// Product ID
Configure::write('ProductId_PremiumSubscription', 'jp.hospia.premium_subscription');

// 投稿記事：Archives 記事カテゴリ表示名
Configure::write('categoryDisp', array(
	'topics' => '情報活用の視点',
	'info' => 'お知らせ',
	'month' => '特集',
	'ranking' => '各種ランキング',
	'policy' => 'サイトポリシー',
	'news' => '病院ニュース',
	'list' => '病院リスト'
));

// 会員の職業
Configure::write('jobs', array(
	'病院経営者・経営幹部' => '病院経営者・経営幹部',
	'病院職員（医師）' => '病院職員（医師）',
	'病院職員（医師以外）' => '病院職員（医師以外）',
	'その他医療機関勤務' => 'その他医療機関勤務',
	'官公庁・行政関係者' => '官公庁・行政関係者',
	'健康保険事業関係者' => '健康保険事業関係者',
	'医療関連企業・団体勤務' => '医療関連企業・団体勤務',
	'一般企業・団体勤務' => '一般企業・団体勤務',
	'専門職・自営業・自由業' => '専門職・自営業・自由業',
	'研究者・教員・学生' => '研究者・教員・学生',
	'パート・アルバイト' => 'パート・アルバイト',
	'主婦・無職 その他'
));

date_default_timezone_set('Asia/Tokyo');
