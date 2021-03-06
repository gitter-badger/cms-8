<?php
session_start();
$uiStructureId = $_REQUEST['uisId'];
$panel_id = $_REQUEST["panelId"];
$container_id = $_REQUEST["containerId"];
?>
<div class="header-pane tabs-bar thin">
  <h1 id="uis-panel-title">
    <span>tr{Add}</span>tr{Panel}
  </h1>

  <ul class="nav nav-pills">
    <li class="active"><a href="#properties" data-toggle="tab">Properties</a></li>

    <li><a href='#size-layout' data-toggle='tab'>Size & Layout</a></li>
  </ul>
</div>

<div class="form-content tabs-bar">
  <div class="tab-content">
    <div class="tab-pane active" id="properties">
      <form id="uis-panel" >
        <div class="block-row">
          <input type="hidden" name="cmd" id="cmd" >
          <system-field class="field col-xs-12">
            <label>tr{ID}</label>
            <input id="style_id" name="style_id" class="text-field" v-model="styleIdText">
          </system-field> 
        </div>
        <div class="block-row">
          <system-field class="field col-xs-12">
            <label>tr{Class}</label>
            <input id="style_class" name="style_class" class="text-field" v-on:keyup.space="updateStyleClasses()" v-on:blur="updateStyleClasses()"| v-model="styleClassesText">            
          </system-field>  

          <div class="col-xs-12">
            <label class="small block-row" id="used-classes">
              <span class='tag label label-info'
                    v-for="class in layoutClasses">
                {{ class }}
              </span>
            </label>
          </div>          
        </div>

        <div class="block-row">
          <div class="col-xs-12">
            <h3>Classes</h3>
            <div class=" options-panel" id="available-classes">
              <label class="btn btn-default" 
                     v-bind:class=" { 'active' : isSelected(class) } "
                     v-for="class in templateClasses" 
                     v-on:click="toggleClass(class)">                
                {{ class }}
              </label>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!--<div class="tab-pane" id="appearance">
      <form id="appearance-conf" >
    <?php include 'uis-panel-appearance.php'; ?>
      </form>
    </div>-->

    <div class="tab-pane" id="size-layout">
      <?php include 'widget-form/size-layout.php'; ?>
    </div>
  </div>
</div>

<div id="uis-panel-actions" class="footer-pane actions-bar action-bar-items"> </div>

<script  type="text/javascript">

  (function () {
    var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='<?= $panel_id ?>']");

    var panelVue = new Vue({
      el: '#uis-panel',
      data: {
        panelId: '<?= $panel_id ?>',
        styleIdText: '<?= $row['style_id'] ?>',
        selectedClasses: [],
        layoutClasses: [],
        tempClasses: [],
        templateClasses: <?= json_encode(EWCore::parse_css_clean(EW_PACKAGES_DIR . "/rm/public/{$_REQUEST['template']}/template.css", 'panel')); ?>
      },
      computed: {
        styleClassesText: {
          set: function (value) {
            this.tempClasses = value;
          },
          get: function () {
            var _this = this;

            var all = _this.selectedClasses.filter(function (item, pos, source) {
              return item && source.indexOf(item) === pos && _this.layoutClasses.indexOf(item) === -1;
            });

            return all.length ? all.join(' ') + ' ' : '';
          }
        },
        usedClasses: function () {
          var all = this.layoutClasses.concat(this.selectedClasses);

          return all.filter(function (item, pos, source) {
            return item && source.indexOf(item) === pos;
          });
        }
      },
      methods: {
        isSelected: function (className) {
          return this.selectedClasses.indexOf(className) !== -1 || this.layoutClasses.indexOf(className) !== -1;
        },
        toggleClass: function (className) {
          var index = this.selectedClasses.indexOf(className);
          if (index !== -1) {
            this.selectedClasses.splice(index, 1);
          } else {
            this.selectedClasses.push(className);
          }

          this.$nextTick(function () {
            $("#style_class").change();
          });
        },
        updateStyleClasses: function () {
          this.selectedClasses = this.tempClasses.split(' ');
        }
      }
    });

    var classes = panel.prop('class') ? panel.prop('class').replace('panel', '').split(' ').filter(Boolean) : [];

    panelVue.layoutClasses = populateLayout(classes);
    panelVue.selectedClasses = classes;

    var $sizeAndLayout = $("#size-layout");
    $sizeAndLayout.find("input").change(function (event) {
      panelVue.layoutClasses = readLayoutClasses();
    });

    panelVue.$nextTick(function () {
      $("#style_class").change();
      panelVue.layoutClasses = readLayoutClasses();
    });

    function populateLayout(classes) {
      var layoutClasses = [];

      var $sizeAndLayout = $("#size-layout");
      $.each($sizeAndLayout.find('input:radio, input:checkbox'), function (k, field) {
        var $v = $(field), value = $v.val();
        $.each(classes, function (i, className) {
          if (value === className) {
            $v.click();
            $v.prop("checked", true);
            layoutClasses.push(classes.splice(i, 1)[0]);
          }
        });
      });

      $.each($sizeAndLayout.find('input[data-slider]'), function (k, field) {
        $.each(classes, function (i, className) {
          if (!className)
            return;

          var sub = className.match(/(\D+)(\d*)/);
          if (sub && $(field).attr("name") === sub[1]) {
            $(field).val(sub[2]).change();
            layoutClasses.push(classes.splice(i, 1)[0]);
          }
        });
      });

      return layoutClasses.filter(Boolean);
    }

    function readLayoutClasses() {
      var layoutClasses = [];

      $.each($sizeAndLayout.find("input[data-slider]:not(:disabled)"), function (k, v) {
        if (v.value) {
          layoutClasses.push(v.name + v.value);
        }
      });

      $.each($sizeAndLayout.find("input:radio:checked:not(:disabled), input:checkbox:checked:not(:disabled)"), function (k, v) {
        layoutClasses.push($(v).val());
      });

      return layoutClasses.filter(Boolean);
    }

    function UISPanel() {
      this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addPanel, this), {
        display: "none"
      }, 'uis-panel-actions');

      this.bEdit = EW.addAction("tr{Save}", $.proxy(this.updatePanel, this), {
        display: "none"
      }, 'uis-panel-actions');

      $("#appearance-conf input[name='title']").change(function () {
        if ($(this).val() == "") {
          $('#title-text').prop('disabled', true);
        } else {
          $('#title-text').prop('disabled', false);
        }
      });

      this.panelId = "<?= $panel_id ?>";
      this.containerId = "<?= $container_id ?>";

      if (this.panelId) {
        var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
        var panelParams = (panel.attr("data-panel-parameters")) ? $.parseJSON(panel.attr("data-panel-parameters")) : {};
        EW.setFormData("#appearance-conf", panelParams);

        $('#uis-panel-title').html('<span>tr{Edit}</span>tr{Panel}');

        this.bAdd.comeOut(200);
        this.bEdit.comeIn(300);
      } else {
        this.bAdd.comeIn(300);
        this.bEdit.comeOut(200);
      }
    }

    // Create and add new div to the page
    UISPanel.prototype.addPanel = function (pId) {
      var self = this;
      //EW.lock(neuis.currentDialog, "...");
      var params = $("#appearance-conf").serializeJSON();
      var div = $("<div data-panel='true'></div>");

      var attrs = {
        id: panelVue.styleIdText,
        'data-panel-parameters': params,
        class: 'panel ' + panelVue.usedClasses.join(' ')
      };

      div.attr(attrs);

      var containerElement = $('#fr').contents().find("body #base-content-pane div[data-panel-id='" + this.containerId + "']");

      if (containerElement.hasClass("block")) {
        containerElement.append(div);
      } else if (!self.containerId) {
        var block = $("<div></div>");
        block.prop("id", $("#style_id").val());
        block.attr("data-panel-parameters", params);
        block.prop("class", "panel row " + $("#used-classes").text());
        $('#fr').contents().find('body #base-content-pane').append(block);
      } else {
        containerElement.children(".row").append(div);
      }

      $('#inspector-editor').trigger('refresh');
      $.EW('getParentDialog', $('#uis-panel')).trigger('close');
    };

    UISPanel.prototype.updatePanel = function (pId) {
      var params = $('#appearance-conf').serializeJSON();
      var panel = $('#fr').contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");

      var attrs = {
        id: panelVue.styleIdText,
        'data-panel-parameters': params,
        class: 'panel ' + panelVue.usedClasses.join(' ')
      };

      panel.attr(attrs);

      $.EW("getParentDialog", $("#uis-panel")).trigger("close");
    };

    uisPanel = new UISPanel();
  })();
  var uisPanel;


</script>