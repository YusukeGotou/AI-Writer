<?php
/*
Plugin Name:AI Writer
Plugin URI: https://github.com/YusukeGotou
Description: AIライティングができるワードプレスプラグインです。
Version: 1.0
Author: Yusuke Goto
Author URI: https://gotoyusuke.com/
Text Domain: Yusuke Goto
*/

require_once trailingslashit( dirname( __FILE__ ) ) . 'includes/chatgpt.php';

function add_admin_style(){
	$path_css = plugins_url().'/css/main.css';
	wp_enqueue_style('admin_style', $path_css);
}
add_action('admin_enqueue_scripts', 'add_admin_style');

function my_user_meta($profile)
{
	$profile['chatGptApiKey']='ChatGPT APIKey';
	return $profile;
}
add_filter('user_contactmethods', 'my_user_meta', 10, 1);

add_action('admin_menu', 'ai_menu_page');
function ai_menu_page()
{
	add_menu_page('AI Writing', 'AI Writing', 'manage_options', 'ai_menu_page', 'add_ai_menu_page', 'dashicons-admin-generic', 4);
}

function add_ai_menu_page(){?>
   <div class="wrap">
	   <h2>AIライティング</h2>
	   <?php echo do_shortcode('[aiPost]'); ?>
   </div>
<?php
}
add_action('admin_menu', 'add_custom_submenu_page');

function add_custom_submenu_page()
{
	add_submenu_page('ai_menu_page', 'ChatGPT', 'ChatGPT', 'manage_options', 'chatgpt', 'gpt', 1);
}
 
function gpt()
{?>
   <div class="wrap">
	  <h2>ChatGPT</h2>
	      <?php echo do_shortcode('[ai]'); ?>
   </div>
<?php
}

function keyword() { 
	$article = $_POST['article'];
	$keyword = $_POST['keyword'];
	$title = $_POST['title'];
	echo $title,$article;
	if(mb_strlen($article) > 0){
		$my_post = array(
			'post_title'    => $title,
			'post_content'  => $article,
			'post_status'   => 'draft',
		);
		wp_insert_post( $my_post );
		echo "下書き保存しました";
	}else{
		echo " ";	
	}
	$domain = get_home_url();
?>
    <form action="<?php echo $domain ?>/wp-admin/admin.php?page=ai_menu_page" method="post">
		<h2>Keyword Setting</h2>
		<p>Main Keyword：<input type="text" name="keyword" value="<?php echo $keyword ?>"></p>
		<p>Sub Keyword ：<input type="text" name="keyword1" value=""></p>
		<h2>Mumber of headings</h2>
		<select name="num">
			<option value="1">1</option>
			<option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5" selected>5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
	   </select>
	   <h2>共起語</h2>
		<p>共起語1：<input type="text" name="connection1" value=""></p>
		<p>共起語2：<input type="text" name="connection2" value=""></p>
		<p>共起語3：<input type="text" name="connection3" value=""></p>
		<input type="submit" value="AI Writing Start">
	</form>
<?php
}
add_shortcode('ai', 'keyword');

function aiPost() { 
	$keyword = $_POST['keyword'];
	$keyword1 = $_POST['keyword1'];
	$num = $_POST['num'];
	$connection1 = $_POST['connection1'];
	$connection2 = $_POST['connection2'];
	$connection3 = $_POST['connection3'];
	if(mb_strlen($keyword) > 0){
		$body = chatgpt_writing($keyword,$keyword1,$num,$connection1,$connection2,$connection3);
		$str='<style>.ai-txt{display: inline-block;width: 100%;padding: 10px;border: 1px solid #999;box-sizing: border-box;background: #f2f2f2;margin: 0.5em 0;line-height: 1.5;height: 6em;}</style>';
		$str.='<form action="'.$domain.'/wp-admin/admin.php?page=chatgpt" method="post">';
		$str.='<p>Main keyword:<input type="text" class="txt" name="keyword" value="'.$keyword .'" ></p>';
		$str.='<p>Title:<input type="text" class="ai-txt" name="title" value="'.$keyword . " ".$keyword1.'"></p>';
		$str.='<p>Index:<input type="text" class="ai-txt" name="article" value="'.$body.'"></p>';
		$str.='<p><input type="submit" value="Add Draft"></p>';
		$str.='</form>';
		return $str ;
	}else{
		
	}
}
add_shortcode('aiPost', 'aiPost');