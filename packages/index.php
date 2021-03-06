<?php

//$before = microtime(true);
/* echo '<pre>' . var_export($_SERVER, true) . '</pre>';
  echo '<pre>' . var_export($_REQUEST, true) . '</pre>';
  die(); */
ini_set('session.cookie_httponly', 1);
session_start();
header_remove("X-Powered-By");

ob_start();
$app_configs = require '../config/app.php';
require '../core/EWCore.class.php';
$env_variables = null;
foreach ($app_configs['DOMAINS'] as $key => $value) {
  if (in_array($_SERVER['HTTP_HOST'], $value)) {
    define('EW_ENVIROMENT', $key);

    $env_variables = $app_configs['VARIABLES'][$key];
    break;
  }
}

if (is_null($env_variables)) {
  die(EWCore::log_error(500, 'Could not find enviroments variables', [
      'domain' => $_SERVER['HTTP_HOST']
  ]));
}

define('EW_DIR', $env_variables['EW_DIR']);
// URL path the refer to EverythigWidget root. if EverythingWidget is in the root then '/'
define('EW_DIR_URL', $env_variables['EW_DIR_URL']);
define('EW_ROOT_DIR', rtrim($_SERVER['DOCUMENT_ROOT'] . $env_variables['EW_DIR'], '/'));
define('EW_CACHE_PATH', $env_variables['EW_CACHE_PATH']);
define('EW_CACHE_DIR', EW_ROOT_DIR . EW_CACHE_PATH);
define('EW_PACKAGES_DIR', EW_ROOT_DIR . '/packages');
define('EW_TEMPLATES_DIR', EW_ROOT_DIR . '/packages/rm/public/templates');
define('EW_WIDGETS_DIR', EW_ROOT_DIR . '/widgets');
define('EW_MEDIA_DIR', EW_ROOT_DIR . '/packages/rm/public/media');
define('HOST_URL', 'http://' . $_SERVER['SERVER_NAME']);


ob_end_clean();
//if (ob_get_level()) {
//  ob_end_clean();
//}

$_file = null;
error_reporting(E_WARNING | E_ERROR | E_PARSE);

$ew_core = new EWCore();
$ew_core->init();

//ini_set('display_errors', '1');

EWCore::set_default_locale("admin");
//EWCore::set_db_connection(get_db_connection());

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
if (substr($path, -1) === '/') {
  $path = substr($path, 0, -1);
}

// Decode url to a normal string
$path = urldecode($path);
if (strpos($path, '?') !== false)
  $path = substr($path, 0, strpos($path, '?'));
$elements = explode('/', $path);
$parameter_index = 0;
if ($elements[0] && strpos('/' . EW_DIR, $elements[0])) {
  $root_dir = array_shift($elements);
  //$parameter_index = 1;
}

// Check the language parameter
$language = 'en';
$default_language = EWCore::read_setting('ew/language');

if ($default_language) {
  $language = $default_language;
} else {
  $default_language = 'en';
}

if (preg_match("/^([^-]{2})$/", $elements[$parameter_index], $match)) {
  $language = $match[0];
  $_REQUEST['_url_language'] = $language;
  array_shift($elements);
}

$_REQUEST['_language'] = $language;

$url_language = ($language == $default_language) ? '' : $language . '/';

// Set protocol to https if detected
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

// Set the language for the root url
if ($_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443') {
  $host_url = $protocol . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
  $u = $protocol . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . EW_DIR_URL . '/' . $url_language;
} else {
  $host_url = $protocol . $_SERVER['SERVER_NAME'];
  $u = $protocol . $_SERVER['SERVER_NAME'] . EW_DIR_URL . '/' . $url_language;
}

define('HOST_URL', $host_url);
define('EW_ROOT_URL', $u);
define('EW_CACHE_URL', EW_ROOT_URL . basename(EW_CACHE_PATH));
define('CURRENT_URL', $host_url . $_SERVER['REQUEST_URI']);

$resource_types = [
    'html',
  // All the HTML resources should be hosted here.
    'api',
  // All the api resources should be hosted here.
    'public',
  // All the publicly available resources should be hosted here.
    'develop',
    'test'
];

// Check the app parameter
$resource_type = 'html';
$app_name = 'webroot';
if (strpos($elements[$parameter_index], '-') === 0 || strpos($elements[$parameter_index], '@') === 0) {
  $app_name = substr($elements[$parameter_index], 1);
  $parameter_index++;

  if (count($elements) >= 3 && $elements[$parameter_index]) {
    $resource_type = $elements[$parameter_index];
    $parameter_index++;
  }
} else if (in_array($elements[$parameter_index], $resource_types)) {
  if (isset($elements[$parameter_index + 1])) {
    $app_name = $elements[$parameter_index + 1];
    $resource_type = $elements[$parameter_index];
    $parameter_index += 2;
  }
}

$section_name = null;
if (isset($elements[$parameter_index]) && preg_match("/^([^\.]*)$/", $elements[$parameter_index], $match)) {
  $section_name = $elements[$parameter_index];
  $_REQUEST['_module_name'] = $section_name;

  $parameter_index++;
}

$function_name = null;
if (isset($elements[$parameter_index])) {
  $function_name = $elements[$parameter_index];
  $_REQUEST['_method_name'] = $function_name;

  $rest_of_elements = array_slice($elements, $parameter_index);
  $file_uri = implode('/', $rest_of_elements);
  $_file = preg_replace('{/$}', '', $file_uri);
  $_REQUEST['_file'] = $_file;

  $parameter_index++;
}

// set default user group if no user group has been spacified
if (!isset($_SESSION['EW.USER_GROUP_ID'])) {
  $_SESSION['EW.USER_GROUP_ID'] = /* json_decode(EWCore::get_default_users_group(), true)["id"] */
      1;
}

$inputs = [];
parse_str(file_get_contents('php://input'), $inputs);

$request_params = array_merge($_REQUEST, $inputs);

$RESULT_CONTENT = $ew_core->process_request_command($app_name, $resource_type, $section_name, $function_name, $request_params);

function translate($match) {
  global $language;
  return EWCore::translate_to_locale($match, $language);
}

// show the result
if ($RESULT_CONTENT) {
  echo preg_replace_callback("/tr(\:\w*)?\{(.*?)\}/", 'translate', $RESULT_CONTENT);
  //  echo $RESULT_CONTENT;
} else {
  echo 'RESULT_CONTENT: EMPTY';
}
//$after = microtime(true);
//echo ($after-$before) . " sec/serialize\n";

