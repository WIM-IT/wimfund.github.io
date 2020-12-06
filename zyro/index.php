<?php
	error_reporting(E_ALL); @ini_set('display_errors', true);
	$tz = @date_default_timezone_get(); @date_default_timezone_set($tz ? $tz : 'UTC');
	require_once dirname(__FILE__).'/polyfill.php';
	$pages = array(
		'0'	=> array('id' => '1', 'alias' => '', 'file' => '1.php','controllers' => array()),
		'1'	=> array('id' => '4', 'alias' => '关于WIM', 'file' => '4.php','controllers' => array()),
		'2'	=> array('id' => '2', 'alias' => '新闻动态', 'file' => '2.php','controllers' => array()),
		'3'	=> array('id' => '5', 'alias' => '人才招聘', 'file' => '5.php','controllers' => array()),
		'4'	=> array('id' => '3', 'alias' => '联系我们', 'file' => '3.php','controllers' => array()),
		'5'	=> array('id' => '6', 'alias' => '智德博客1', 'file' => '6.php','controllers' => array()),
		'6'	=> array('id' => '7', 'alias' => '智德博客2', 'file' => '7.php','controllers' => array()),
		'7'	=> array('id' => '8', 'alias' => '智德博客3', 'file' => '8.php','controllers' => array()),
		'8'	=> array('id' => '9', 'alias' => '智德博客4', 'file' => '9.php','controllers' => array()),
		'9'	=> array('id' => '10', 'alias' => '股权投资', 'file' => '10.php','controllers' => array())
	);
	$forms = array(

	);
	$langs = null;
	$def_lang = null;
	$base_lang = 'zh';
	$site_id = "e1c31be4";
	$websiteUID = "2771d58d563d2cc42e0d77ba3864bbca2e6110336cf9b89091609cffb9ed505c6d32c6c62b65bf97";
	$base_dir = dirname(__FILE__);
	$base_url = '/';
	$user_domain = 'wimfund.com';
	$home_page = '1';
	$mod_rewrite = true;
	$show_comments = false;
	$comment_callback = "http://uk.zyro.com/zh-HK/comment_callback/";
	$user_key = "CF/4M5FJP73RFHk=";
	$user_hash = "e113d56e520854f7";
	$ga_code = (is_file($ga_code_file = dirname(__FILE__).'/ga_code') ? file_get_contents($ga_code_file) : null);
	require_once dirname(__FILE__).'/src/SiteInfo.php';
	require_once dirname(__FILE__).'/src/SiteModule.php';
	require_once dirname(__FILE__).'/functions.inc.php';
	$siteInfo = SiteInfo::build(array('siteId' => $site_id, 'websiteUID' => $websiteUID, 'domain' => $user_domain, 'homePageId' => $home_page, 'baseDir' => $base_dir, 'baseUrl' => $base_url, 'defLang' => $def_lang, 'baseLang' => $base_lang, 'userKey' => $user_key, 'userHash' => $user_hash, 'commentsCallback' => $comment_callback, 'langs' => $langs, 'pages' => $pages, 'forms' => $forms, 'modRewrite' => $mod_rewrite, 'gaCode' => $ga_code, 'gaAnonymizeIp' => false, 'port' => null, 'pathPrefix' => null, 'useTrailingSlashes' => true,));
	$requestInfo = SiteRequestInfo::build(array('requestUri' => getRequestUri($siteInfo->baseUrl),));
	SiteModule::init(null, $siteInfo);
	list($page_id, $lang, $urlArgs, $route) = parse_uri($siteInfo, $requestInfo);
	$preview = false;
	$lang = empty($lang) ? $def_lang : $lang;
	$requestInfo->{'page'} = (isset($pages[$page_id]) ? $pages[$page_id] : null);
	$requestInfo->{'lang'} = $lang;
	$requestInfo->{'urlArgs'} = $urlArgs;
	$requestInfo->{'route'} = $route;
	handleTrailingSlashRedirect($siteInfo, $requestInfo);
	SiteModule::setLang($requestInfo->{'lang'});
	$hr_out = '';
	$page = $requestInfo->{'page'};
	if (!is_null($page)) {
		handleComments($page['id'], $siteInfo);
		if (isset($_POST["wb_form_id"])) handleForms($page['id'], $siteInfo);
	}
	ob_start();
	if ($page) {
		$fl = dirname(__FILE__).'/'.$page['file'];
		if (is_file($fl)) {
			ob_start();
			include $fl;
			$out = ob_get_clean();
			$ga_out = '';
			if ($lang && $langs) {
				foreach ($langs as $ln => $default) {
					$pageUri = getPageUri($page['id'], $ln, $siteInfo);
					$out = str_replace(urlencode('{{lang_'.$ln.'}}'), $pageUri, $out);
				}
			}
			if (is_file($ga_tpl = dirname(__FILE__).'/ga.php')) {
				ob_start(); include $ga_tpl; $ga_out = ob_get_clean();
			}
			$out = str_replace('<ga-code/>', $ga_out, $out);
			$out = str_replace('{{base_url}}', getBaseUrl(), $out);
			$out = str_replace('{{curr_url}}', getCurrUrl(), $out);
			$out = str_replace('{{hr_out}}', $hr_out, $out);
			header('Content-type: text/html; charset=utf-8', true);
			echo $out;
		}
	} else {
		header("Content-type: text/html; charset=utf-8", true, 404);
		if (is_file(dirname(__FILE__).'/404.html')) {
			include '404.html';
		} else {
			echo "<!DOCTYPE html>\n";
			echo "<html>\n";
			echo "<head>\n";
			echo "<title>404 Not found</title>\n";
			echo "</head>\n";
			echo "<body>\n";
			echo "404 Not found\n";
			echo "</body>\n";
			echo "</html>";
		}
	}
	ob_end_flush();

?>