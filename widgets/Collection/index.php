<?phpsession_start();global $EW;$links = $widget_parameters["link"];$icnons = $widget_parameters["container_id"];//$result = mysql_query("SELECT * FROM menus , sub_menus WHERE menus.id = sub_menus.menu_id AND menus.id = '$menuId' ORDER BY sub_menus.order") or die(mysql_error());?><div class="page">   <?php   if (gettype($links) == "array")   {      for ($i = 0; $i < count($links); $i++)      {         $sub_menus = null;         $link = json_decode($links[$i], true);         if ($link["type"] == "link")         {            $linkURL = $link["url"];         }         else if ($link["type"] == "uis")         {            $layout = admin\WidgetsManagement::get_layout($link["id"]);            $html = $layout["template_body"];            //$linkURL = '#';            //$sub_menus = EWCore::get_widget_feeder("menu", $link["feederName"]);            //$sub_menus = json_decode($sub_menus, TRUE);         }         else         {            //$linkURL = EW_DIR . $link["type"] . '/' . $link["id"];         }         echo "<div class='page-slide item' data-not-editable=true>$html</div>";      }   }   else   {      if ($link["type"] == "link")      {         $linkURL = $link["url"];      }      else if ($link["type"] == "uis")      {         $layout = admin\WidgetsManagement::get_layout($link["id"]);            $html = $layout["template_body"];         //$linkURL = '#';         //$sub_menus = EWCore::get_widget_feeder("menu", $link["feederName"]);         //$sub_menus = json_decode($sub_menus, TRUE);      }      else      {         //$linkURL = EW_DIR . $link["type"] . '/' . $link["id"];      }      echo "<div class='page-slide item' data-not-editable=true>$html</div>";   }   ?></div><script></script>