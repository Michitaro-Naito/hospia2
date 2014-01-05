<?php
class PostController extends AppController {

	public $components = array('Data');

	/**
	 * トップページ
	 */
	public function Index() {
	}

	/**
	 * 投稿内容が見つからない場合の遷移処理
	 */
	public function NotFound() {
	}

	/**
	 * 投稿記事の表示
	 */
	public function Archives($param1 = null, $param2 = null){

		// 第一パラメータに何も指定されていない場合はNotFound用処理を実行する
		if (!isset($param1)) {
			$this->setAction("notfound");
			return;
		}

		// 第一パラメータに"category"が指定されていた場合はカテゴリ別一覧表示用の処理を実行する
		if ($param1 === "category") {
			$this->setAction("category", $param2);
			return;
		}

		// 第一パラメータに指定されたIDの投稿記事を検索する
		$posts = $this->Data->GetPostsByPostId($param1);

		// 投稿記事が見つからなかった場合はNotFound用処理を実行する
		if (!$posts) {
			$this->setAction("notfound");
			return;
		}

		// 検索して取得した投稿記事のデータを遷移先のページに渡す
		$this->set("posts", $posts);
	}

	/**
	 * カテゴリ別の投稿記事の表示
	 */
	public function Category($category = null) {

		// 第一パラメータに何も指定されていない場合はNotFound用処理を実行する
		if (!isset($category)) {
			$this->setAction("notfound");
			return;
		}

		// カテゴリがtopic(情報活用の視点), info(お知らせ), month(特集), ranking(各種ランキング)以外の場合は
		// NotFound用処理を実行する
		if (!in_array($category, array('topics', 'info', 'month', 'ranking'))) {
			$this->setAction("notfound");
			return;
		}

		// 第一パラメータに指定されたカテゴリの投稿記事を検索する
		$posts = $this->Data->GetPostsByCategory($category);

		// 投稿記事が見つからなかった場合はNotFound用処理を実行する
		if (!$posts) {
			$this->setAction("notfound");
			return;
		}

		// 検索して取得した投稿記事のデータとカテゴリを遷移先のページに渡す
		$this->set("posts", $posts);
		$this->set("category", $category);
	}

	/**
	 * サイトポリシーの表示
	 */
	public function Policy() {

		// カテゴリがpolicyの投稿記事を検索する
		$posts = $this->Data->GetPostsByCategory('policy');

		// 投稿記事が見つからなかった場合はNotFound用処理を実行する
		if (!$posts) {
			$this->setAction("notfound");
			return;
		}

		// 検索して取得した投稿記事のデータを遷移先のページに渡す
		$this->set("posts", $posts);
	}
}