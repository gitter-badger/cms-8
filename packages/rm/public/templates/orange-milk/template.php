<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template
 *
 * @author Eeliya
 */
class template extends TemplateControl {

  public function get_template_body($html_body, $template_settings) {
    return $html_body;
  }

  public function get_template_script($template_settings) {
    \webroot\WidgetsManagement::add_html_script(['include' => '/rm/public/js/scroll-it/scroll-it.js']);
    \webroot\WidgetsManagement::add_html_link([
        'place' => 'body',
        'rel' => 'stylesheet',
        'href' => 'public/rm/templates/orange-milk/fonts.css'
    ]);
    \webroot\WidgetsManagement::add_html_link([
        'place' => 'body',
        'rel' => 'stylesheet',
        'href' => 'https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'
    ]);

    ob_start();
    include 'template.js';
    return ob_get_clean();
  }

  public function get_template_settings_form() {
    ob_start();
    ?>
    <div class="form-block">
      <label class="checkbox">
        Single Page Website
        <input type="checkbox" id="spw" name="spw" value="true"><i></i>
      </label>
    </div>
    <div id="spw-cp" class="form-block">
      <div class="block-row mt">
        <select class="text-field" id="menu-id" name="menu-id" data-label="Main Menu ID">
          <option value=''></option>
        </select>
      </div>
    </div>
    <script>
      (function () {
        var template_settings_form = $("#template_settings_form");
        var widgets = [];

        function initSelect(select, value) {
          var $select = $(select).empty();
          $select.append("<option value=''></option>");

          $.each(widgets, function (i, widget) {
            var $widget = $(widget);
            if ($widget.attr("id")) {
              var selected = (value === $widget.attr("id")) ? "selected=true" : "";
              $select.append("<option value='" + $widget.attr("id") + "' " + selected + ">" +
                  $widget.attr("id") + "</option>");
            }
          });
        }

        System.ui.forms.uis_form.inspectorEditor.off("refresh.template");
        System.ui.forms.uis_form.inspectorEditor.on("refresh.template", function (e, data) {
          var currentValue = $("#menu-id").val();
          var currentValueSlider = $("#page-slider").val();
          initSelect("#menu-id", currentValue);
          initSelect("#page-slider", currentValueSlider);
        });

        template_settings_form.on("refresh", function (e, data) {
          var spa = $("#spw");
          if (!spa.is(":checked")) {
            $("#spw-cp :input").attr('disabled', true);
            $("#spw-cp").hide();
          }

          widgets = System.ui.forms.uis_form.getLayoutWidgets();

          initSelect("#menu-id", data["menu-id"]);
          initSelect("#page-slider", data["page-slider"]);

          spa.off("change").on("change", function () {
            if (spa.is(":checked")) {
              $("#spw-cp :input").attr('disabled', false);
              $("#spw-cp").stop().animate({
                height: "toggle"
              }, 400, "Power2.easeOut");

              //System.ui.forms.uis_form.updateTemplateBody();
              widgets = System.ui.forms.uis_form.getLayoutWidgets();
              initSelect("#menu-id", data["menu-id"]);
              initSelect("#page-slider", data["page-slider"]);
            } else {
              $("#spw-cp").stop().fadeOut(200, function () {
                $("#spw-cp :input").attr('disabled', true);
              });
            }
          });
        });

        template_settings_form.on("getData", function (e) {
          var data = $.parseJSON($("#template_settings_form").serializeJSON());
          System.ui.forms.uis_form.setTemplateSettings(data);
        });
      })();
    </script>
    <?php
    return ob_get_clean();
  }

  //put your code here
}
