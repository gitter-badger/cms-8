<?phpsession_start();$feeder = $widget_parameters["feeder"];if (!is_array($feeder)) {  $feeder = json_decode($widget_parameters["feeder"], TRUE);}$feeder_id = $feeder["feederId"];$item_id = $feeder["id"];if ($widget_parameters["default-content"] == "yes") {  if ($_REQUEST["_module_name"]) {    $feeder_id = $_REQUEST["_module_name"];  }  $item_id = $_REQUEST["_method_name"];  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder_by_url($feeder_id);}else {  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder_id);}$token = $_REQUEST["$feeder_id-token"];$page_size = isset($widget_parameters["items-count"]) ? intval($widget_parameters["items-count"]) : 10;$row_num = ($token * $page_size) + $page_size;$order = null;if ($widget_parameters["order"] !== " default") {  $order = $widget_parameters["order"];}if (!$feeder_id) {  return;}if ($feeder_obj->feeder_type !== "list") {  //echo "<p>Feeder type is invalid. Need a list feeder type</p>";  return;}if (!$token) {  $token = 0;}$items_list = EWCore::call_api($feeder_obj->api_url, [            "id"        => $item_id,            'params'    => $feeder['params'],            "token"     => $token * $page_size,            "size"      => $page_size,            "order_by"  => $order,            '_language' => $_REQUEST['_language']        ]);$items_count = $items_list["collection_size"];$items = $items_list["data"];$page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);if ($widget_parameters["content_fields"]) {  $fields = $widget_parameters["content_fields"];  if (is_string($fields)) {    $fields = [$fields];  }  foreach ($items as $i => $item) {    $items[$i]['html'] = '';    $items[$i]['item-link'] = 'See more';    foreach ($fields as $field) {      $field_data = $item['content_fields'][$field];      $tag = $field_data['tag'];      if (isset($tag)) {        if ($tag === 'img') {          $items[$i]['html'] .= "<img class='$field' src='{$field_data['src']}'/>";        }        else {          $items[$i]['html'] .= "<$tag class='$field'>{$field_data['content']}</$tag>";        }      }      if ($item['content_fields']['@widget/list/item-link']) {        $items[$i]['item-link'] = $item['content_fields']['@widget/list/item-link']['content'];      }    }  }}if ($items_list["header"]) {  echo $items_list["header"];}if ($widget_parameters["show_top_buttons"]) {  $list_header_html = "<div class='row list-header'><div class='col-xs-12'>";  $list_header_html .= ($token > 0) ? "<a href='$page?$feeder_id-token=" . ($token - 1) . " class='btn btn-link btn-prev'>{Pervious}</a>" : "";  $list_header_html .= ($row_num < $items_count) ? "<a href='$page?$feeder_id-token=" . ($token + 1) . " class='btn btn-link btn-next'>tr{Next}</a>" : "";  $list_header_html .= "</div></div>";  echo $list_header_html;}?><div class="row list-content">  <?php  $index = 1;  if (isset($items)) {    $list_items_html = "";    if ($widget_parameters["hide_see_more"]) {      foreach ($items as $item) {        $list_items_html .= "<div class='list-item col-xs-{$widget_parameters["col-xs-"]} col-sm-{$widget_parameters["col-sm-"]} col-md-{$widget_parameters["col-md-"]} col-lg-{$widget_parameters["col-lg-"]} {$item["class"]}' style='{$item["style"]}'>"                . "<div class='list-item-cell'>{$item["html"]}"                . "</div></div>";      }    }    else {      foreach ($items as $item) {        $list_items_html .= "<div class='list-item col-xs-{$widget_parameters["col-xs-"]} col-sm-{$widget_parameters["col-sm-"]} col-md-{$widget_parameters["col-md-"]} col-lg-{$widget_parameters["col-lg-"]} {$item["class"]}' style='{$item["style"]}'>"                . "<div class='list-item-cell'>{$item["html"]}"                . "<a class='item-link' href='./articles/{$item["id"]}'>{$item["item-link"]}</a>"                . "</div></div>";        $index++;      }    }    echo $list_items_html;  }  ?></div><?phpif ($widget_parameters["show_bottom_buttons"]) {  $bottom_buttons_html = "<div class='row list-footer'><div class='col-xs-12'>";  $bottom_buttons_html .= ($token > 0) ? "<a href='$page?$feeder_id-token=" . ($token - 1) . "' class='btn btn-link btn-prev'>tr{Pervious}</a>" : "";  $bottom_buttons_html .= ($row_num < $items_count) ? "<a href='$page?$feeder_id-token=" . ($token + 1) . "' class='btn btn-link btn-next'>tr{Next}</a>" : "";  $bottom_buttons_html .= "</div></div>";  echo $bottom_buttons_html;}