ALTER TABLE  `users` ADD  `email` VARCHAR( 255 ) NOT NULL AFTER  `role` ;
ALTER TABLE  `users` ADD  `displayname` VARCHAR( 50 ) NOT NULL AFTER  `email` ;
ALTER TABLE  `users` ADD  `deleted` DATETIME NULL ;
ALTER TABLE  `users` ADD  `active` BOOLEAN NOT NULL AFTER  `deleted` ;

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL auto_increment,
  `hash` varchar(255) default NULL,
  `caller` varchar(255) default NULL,
  `data` varchar(255) default NULL,
  `deadline` datetime default NULL,
  `usecount` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `hashs` (`hash`)
) 

CREATE TABLE `favorite_hospitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
)

create table transaction(
  id int primary key auto_increment,
  created datetime comment '作成日時',
  jwt longtext comment '受信されたJWT',
  payload longtext comment 'デコードされたJWT',
  typ varchar(255) comment 'トランザクションの種類',
  user_id int comment '購入者のユーザーID',
  username varchar(50) comment '購入者のユーザー名',
  display_name varchar(50) comment '購入者の表示名',
  email varchar(255) comment '購入者のメールアドレス',
  order_id varchar(255) comment '注文ID',
  product_id varchar(255) comment '製品ID'
)engine=innodb comment = 'トランザクション(記録のみ)';

create table subscription(
  id int primary key auto_increment,
  user_id int comment '購入者のユーザーID',
  order_id varchar(255) unique comment '注文ID',
  product_id varchar(255) comment '製品ID'
)engine=innodb comment = '現在有効なサブスクリプション(月額課金)';

CREATE TABLE `favorite_hospitals_hospital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_id` int(11) NOT NULL,
  `favorite_hospital_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)

CREATE TABLE IF NOT EXISTS `post` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'レコードの識別ID',
  `post_id` bigint(20) unsigned NOT NULL COMMENT '記事の識別ID',
  `title` text NOT NULL COMMENT '記事のタイトル',
  `content` longtext NOT NULL COMMENT '記事の内容',
  `category` varchar(20) NOT NULL DEFAULT 'topic' COMMENT '記事の種類(情報活用の視点・特集・お知らせ・各種ランキング・サイトポリシー)',
  `status` varchar(20) NOT NULL DEFAULT 'private' COMMENT '記事の公開ステータス(公開・非公開・削除)',
  `created` datetime DEFAULT NULL COMMENT '記事の作成日(UTC時刻)',
  `modified` datetime DEFAULT NULL COMMENT '記事の更新日(UTC時刻)',
  PRIMARY KEY (`ID`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;

create table file(
	id int primary key auto_increment,
	name varchar(255) unique not null comment'ファイル名',
	created datetime not null comment '作成日時',
	modified datetime not null comment '編集日時'
) comment 'アップロードされたファイルの名称';

alter table post add deleted datetime null after modified;
