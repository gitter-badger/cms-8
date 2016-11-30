<?php
session_start();
global $rootAddress, $pageAddress;

$current_path = str_replace(EW_DIR, '', $_SERVER['REQUEST_URI']);
$app = "webroot";

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

webroot\WidgetsManagement::include_html_link(["rm/public/css/bootstrap.css"]);

webroot\WidgetsManagement::add_html_script(["include" => "admin/public/js/lib/bootstrap.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/TweenLite.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/easing/EasePack.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/jquery.gsap.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/plugins/CSSPlugin.min.js"]);

$VIEW = webroot\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$HTML_BODY = $VIEW["body_html"];
$WIDGET_DATA = $VIEW["widget_data"];

$TEMPLATE_LINK = ($_REQUEST["_uis_template"]) ?
        '<link rel="stylesheet" property="stylesheet" type="text/css" id="template-css" href="public/rm/' . $_REQUEST["_uis_template"] . '/template.css" />' : "";

// If template has a 'template.php' then include it
$template_php = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.php';
if (file_exists($template_php)) {
  require_once $template_php;
  $template = new \template();
  //$uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
  $template_settings = $_REQUEST['_uis_template_settings'];

  if (is_array($template_settings)) {
    $template_settings = json_encode($template_settings);
  }

  if (empty($template_settings) || $template_settings === 'null') {
    $template_settings = '{}';
  }

  $TEMPLATE_SCRIPT = "";
  $template_script_dom = $template->get_template_script(json_decode($_REQUEST["_uis_template_settings"], true));
  if ($template_script_dom) {
    $template_script_dom = preg_replace('/\$php\.\$template_settings/', $template_settings, $template_script_dom);
//    $template_script_dom = preg_replace_callback('/\$php\.([\w]*)/', function($match) use ($view_data) {
//      $data = $view_data[$match[1]];
//      return isset($data) ? $data : null;
//    }, $template_script_dom);

    $TEMPLATE_SCRIPT = '<script id="template-script">' . $template_script_dom . '</script>';
  }
}

$currentAppConf = webroot\WidgetsManagement::get_page_info();

$website_title = $currentAppConf["title"];
$page_description = $currentAppConf["description"];
$website_keywords = $currentAppConf["keywords"];
$favicon = $currentAppConf["favicon"];
$google_analytics_id = $currentAppConf["google-analytics-id"];

if ($page_description) {
  \webroot\WidgetsManagement::set_meta_tag([
      'name'    => 'description',
      'content' => $page_description
  ]);
}

$HTML_TITLE = $website_title;
$HTML_KEYWORDS = webroot\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = webroot\WidgetsManagement::get_html_scripts();
$HTML_LINKS = webroot\WidgetsManagement::get_html_links();
$HTML_CSS = webroot\WidgetsManagement::get_html_links_concatinated();
$HTML_META_TAGS = webroot\WidgetsManagement::get_meta_tags();
?>
<!DOCTYPE html> 
<html>
  <head>
    <base href="<?= EW_ROOT_URL ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />  

    <?php
    echo $HTML_META_TAGS;
    echo "<title>$HTML_TITLE</title>";
    echo "<meta name='keywords' content='$HTML_KEYWORDS'/>";
    echo "<link rel='shortcut icon' href='$favicon'>";
    echo "<link rel='apple-touch-icon-precomposed' href='$favicon'>";
    echo '<meta name="msapplication-TileColor" content="#FFFFFF">';
    echo "<meta name='msapplication-TileImage' content='$favicon'>";

    if (isset($google_analytics_id)) {
      ?>
      <script>
        (function (i, s, o, g, r, a, m) {
          i['GoogleAnalyticsObject'] = r;
          i[r] = i[r] || function () {
            (i[r].q = i[r].q || [
            ]).push(arguments)
          }, i[r].l = 1 * new Date();
          a = s.createElement(o),
                  m = s.getElementsByTagName(o)[0];
          a.async = 1;
          a.src = g;
          m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', '<?= $google_analytics_id ?>', 'auto');
        ga('send', 'pageview');
      </script>
      <?php
    }
    ?>
    <script id="widget-data">
      (function () {
        var ew_widget_data = {};
        var ew_widget_actions = {};

<?= $WIDGET_DATA; ?>

        window.ew_widget_data = ew_widget_data;
        window.ew_widget_actions = ew_widget_actions;
      })();
    </script>

    <script src="public/rm/js/webcomponents/webcomponents-lite.min.js"></script>
    <script src="public/rm/js/x-tag/x-tag.min.js"></script>
    <script src="public/rm/js/galaxyjs/galaxy-tags-min.js"></script>
    <script src="public/rm/js/jquery/build.js"></script>
    <script src="public/rm/js/vue/vue.min.js"></script>       


    <?= $HTML_SCRIPTS; ?>
    <?= $TEMPLATE_SCRIPT; ?>      

  </head>
  <body class="<?= EWCore::get_language_dir($_REQUEST["_language"]) ?>">
    <div id="base-content-pane" class="container">
      <?= $HTML_BODY; ?>
    </div>

    <?= $HTML_CSS ?>
    <?= $HTML_LINKS; ?>
    <?= $TEMPLATE_LINK; ?>    
  </body>  
</html>