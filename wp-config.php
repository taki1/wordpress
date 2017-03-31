<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、インストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さずにこのファイルを "wp-config.php" という名前でコピーして
 * 直接編集して値を入力してもかまいません。
 *
 * このファイルは、以下の設定を含みます。
 *
 * * MySQL 設定
 * * 秘密鍵
 * * データベーステーブル接頭辞
 * * ABSPATH
 *
 * @link http://wpdocs.osdn.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 *
 * @package WordPress
 */

// 注意:
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.osdn.jp/%E7%94%A8%E8%AA%9E%E9%9B%86#.E3.83.86.E3.82.AD.E3.82.B9.E3.83.88.E3.82.A8.E3.83.87.E3.82.A3.E3.82.BF 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'wp01');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'dbuser');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'password1');

/** MySQL のホスト名 */
define('DB_HOST', 'localhost');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(XSrE:~|#ek41ao0`Kv*f`,vE^9TW)#/aX;nm:J#06)p$R_fw/Y<+_&kTMC7 {D/');
define('SECURE_AUTH_KEY',  '-Rn4^iDG z:UJ+Vo^V+Q(XXmK#|;KC|Z=w;<sv}UE7-5BXH6jYEEVm;OFHJ5|5:~');
define('LOGGED_IN_KEY',    '4g/|@^wBDKL~Yb+2i0-FZt+F@p81i47iA2?^-.N[>GBl?/zxa}emV]VH42*;/q&p');
define('NONCE_KEY',        'd3^m&+|M^DU,di&{}Bj:YB)]|+R;J7.]0q>aZTC&RgM6n~Ro=H3YqT |=,cPEG_Z');
define('AUTH_SALT',        '1AC|.DCjIu+`{CCy9px=[zO++6e{bwfT+-nE#5Be!=5!0);lX3QQug`m-/(eLw/N');
define('SECURE_AUTH_SALT', 'Ol$=#bZ(>6.?lW)_sWJr#6>MlH&+W.,_kJQ~FY[Riv|_p-bti-;RZ+zPLy:qD(7%');
define('LOGGED_IN_SALT',   '|4grXuc8iS}-HW- 1tHE9vUKdnsTdFq>Zw,M[*$VPrw&r(=+,kX+)+d?6OtkSA}y');
define('NONCE_SALT',       '>wzA#b;x{U{N<CUdY<jld@&m?DRWU/v:e8+c!!x/,RWa gD0*z4f#B79,RYE(]UE');
/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'wp_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 *
 * その他のデバッグに利用できる定数については Codex をご覧ください。
 *
 * @link http://wpdocs.osdn.jp/WordPress%E3%81%A7%E3%81%AE%E3%83%87%E3%83%90%E3%83%83%E3%82%B0
 */
define('WP_DEBUG', false);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
