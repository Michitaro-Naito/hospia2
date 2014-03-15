<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

	Router::parseExtensions('json');
	
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller'=>'home', 'action'=>'index'));
	Router::connect('/hoslist', array('controller'=>'home', 'action'=>'hoslist'));
	Router::connect('/dpc', array('controller'=>'home', 'action'=>'dpc'));
	Router::connect('/toplst', array('controller'=>'home', 'action'=>'toplst'));
	Router::connect('/malady/maladylist.php', array('controller'=>'home', 'action'=>'maladylist'));
	Router::connect('/hosdetail', array('controller'=>'home', 'action'=>'hosdetail'));
	Router::connect('/hosdetail/:wam_id', array('controller'=>'home', 'action'=>'hosdetail'));
	Router::connect('/hoscmp', array('controller'=>'home', 'action'=>'hoscmp'));
	Router::connect('/hoscmp/:wam_id', array('controller'=>'home', 'action'=>'hoscmp'));
	Router::connect('/hosinfo', array('controller'=>'home', 'action'=>'hosinfo'));
	Router::connect('/hosinfo/:wam_id', array('controller'=>'home', 'action'=>'hosinfo'));
	Router::connect('/GoogleWalletPostback', array('controller'=>'Transaction', 'action'=>'GoogleWalletPostback'));
	Router::connect('/wp/gu', array('controller'=>'Post', 'action'=>'Archives', 6));
	Router::connect('/wp/company', array('controller'=>'Post', 'action'=>'Archives', 15));
	Router::connect('/wp/ad', array('controller'=>'Post', 'action'=>'Archives', 19));
	Router::connect('/wp/inquiry', array('controller'=>'Post', 'action'=>'Archives', 23));
	Router::connect('/wp/:action/*', array('controller'=>'post'));
	Router::connect('/malady/aboutwidget.php', array('controller'=>'Widget', 'action'=>'About'));
	Router::connect('/widget/maladywidget.php', array('controller'=>'Widget', 'action'=>'Script'));
	Router::connect('/Compare/*', array('controller'=>'Home', 'action'=>'CompareMdcsByYear'));
	Router::connect('/LineChart/*', array('controller'=>'Home', 'action'=>'CompareInFavoriteGroupByYear'));
	Router::connect('/BubbleChart/*', array('controller'=>'Home', 'action'=>'CompareInFavoriteGroupByBubbles'));
	Router::connect('/Position/*', array('controller'=>'Home', 'action'=>'CompareMdcsByBubbles'));
	Router::connect('/Sitemap/*', array('controller'=>'Home', 'action'=>'Sitemap'));
	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';