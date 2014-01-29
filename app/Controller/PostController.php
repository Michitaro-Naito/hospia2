<?php
class PostController extends AppController {
	public $components = array('Data');
	
	/**
	 * 記事一覧
	 * Admin only.
	 */
	public function Index() {
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		$this->request->data['VM'] = $this->request->query;
		$cond = array();
		if(!empty($this->request->data['VM']['title']))
			$cond['Post.title like'] = "%{$this->request->data['VM']['title']}%";
		$this->paginate = array(
			'paramType'=>'querystring',
			'fields'=>array('Post.id', 'Post.title'),
			'order'=>array('Post.ID'=>'desc'),
			'limit'=>50,
		);
		$posts = $this->paginate('Post', $cond);
		$this->set('posts', $posts);
	}
	
	/**
	 * 記事編集
	 * Admin only.
	 */
	public function Edit($id = null){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		if(empty($this->data)){
			$this->data = $this->Post->findById($id);
		}else{
			if($this->Post->save($this->data)){
				$this->Session->setFlash('Saved!');
				return $this->redirect('/Post');
			}
		}
	}
	
	/**
	 * 記事無効化
	 * Disables a post. Admin only.
	 */
	public function Disable($id = null){
	 	if(!$this->IsAdmin())
			return $this->redirect('/');
		
		if($this->request->isPost()){
			$this->Post->delete($id, false);
			return $this->redirect('/Post');
		}
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

		// カテゴリがtopic(情報活用の視点), info(お知らせ), month(特集), ranking(各種ランキング), news以外の場合は
		// NotFound用処理を実行する
		if (!in_array($category, array('topics', 'info', 'month', 'ranking', 'news'))) {
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
		
		// カテゴリに応じてtitleを設定する
		$titles = array(
			'topics'=>'情報活用の視点',
			'info'=>'お知らせ',
			'month'=>'特集',
			'ranking'=>'ランキング',
			'news'=>'病院ニュース'
		);
		if(array_key_exists($category, $titles))
			$this->set('title', $titles[$category]);
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