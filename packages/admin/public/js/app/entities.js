/* global System, EW, Vue */

(function () {
  System.entity('stage/init-ui-components', init);

  function init() {
    var navMenuVue = new Vue({
      el: '#navigation-menu',
      data: {
        apps: [],
        currentState: null,
        currentApp: null,
        currentSection: null,
        currentSubSection: null,
        isNavTitleIn: false,
        isNavMenuIn: false
      },
      methods: {
        goToState: function (state) {
          if (System.modules['system/' + state]) {
            var lastSelectedSection = System.modules['system/' + state].params['app'] || state;
            return System.app.setNav(lastSelectedSection);
          }

          System.app.setNav(state);
        },
        navMenuIn: function () {
          this.isNavMenuIn = true;
        },
        navMenuOut: function () {
          this.isNavMenuIn = false;
        }
      }
    });

    System.entity('ui/apps', navMenuVue);

    // ------ //

    var primaryActionsVue = new Vue({
      el: '#main-float-menu',
      data: {
        actions: []
      },
      methods: {
        callActivity: function (action) {
          var activityCaller = EW.getActivity(action);
          activityCaller(action.hash);
        }
      }
    });

    System.entity('ui/primary-menu', primaryActionsVue);

    // ------ //

    var appBarVue = new Vue({
      el: '#app-bar',
      data: {
        appTitle: '',
        sectionTitle: '',
        isLoading: false,
        subSections: null,
        currentSubSection: navMenuVue.currentSubSection
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (this.subSections && this.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        },
        currentState: function () {
          return navMenuVue.currentState;
        }
      },
      methods: {
        goTo: function (tab, $event) {
          $event.preventDefault();

          System.app.setNav(navMenuVue.currentApp + '/' + navMenuVue.currentSection + '/' + tab.state);
        },
        goToState: function (state) {
          System.app.setNav(state);
        },
        navTitleIn: function () {
          navMenuVue.isNavTitleIn = true;
        },
        navTitleOut: function () {
          navMenuVue.isNavTitleIn = false;
        }
      }
    });

    System.entity('ui/app-bar', appBarVue);

    // ------ //

    var mainContentVue = new Vue({
      el: '#main-content',
      data: {
        show: false
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (appBarVue.subSections && appBarVue.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        }
      }
    });

    System.entity('ui/main-content', mainContentVue);

    // ------ //

    var linkChooserDialog = {};
    linkChooserDialog.open = function (onSelect) {
      var linkChooserDialog = EW.createModal({
        class: 'center slim'
      });

      System.loadModule({
        url: 'html/admin/content-management/link-chooser/component.php',
        params: {
          contentType: 'content'
        }
      }, function (module) {
        module.scope.onSelect = function (content) {
          onSelect.call(null, content);

          linkChooserDialog.dispose();
        };

        linkChooserDialog.html(module.html);
      });

      return linkChooserDialog;
    };

    System.entity('ui/dialogs/link-chooser', linkChooserDialog);

    // ------ //

    ContentTools.Tools.EWMedia = (function (superClass) {
      System.utility.extend(EWMedia, superClass);

      function EWMedia() {
        return EWMedia.__super__.constructor.apply(this, arguments);
      }

      ContentTools.ToolShelf.stow(EWMedia, 'ew-media');
      EWMedia.label = 'EW Media';
      EWMedia.icon = 'ew-media';
      EWMedia.tagName = 'p';
      EWMedia.canApply = function (element, selection) {
        return true;
      };

      EWMedia.apply = function (item, selection, callback) {
        var app, forceAdd, paragraph, region, _this = this;
        app = ContentTools.EditorApp.get();
        var imageChooserDialog = EW.createModal({
          autoOpen: false,
          class: "center"
        });

        System.loadModule({
          id: 'media-chooser',
          url: 'html/admin/content-management/link-chooser/link-chooser-media.php',
          params: {
            callback: ''
          }
        }, function (module, html) {
          imageChooserDialog.html(html);

          var ref = _this._insertAt(item), node = ref[0], index = ref[1];
          module.scope.selectMedia = function (item) {
            if (item === false) {
              imageChooserDialog.dispose();
              return;
            }

            switch (item.type) {
              case 'text':
                var text = new ContentEdit.Text('p', {}, item.text);
                if (node.parent()) {
                  node.parent().attach(text, index);
                } else {
                  var firstRegion = app.orderedRegions()[0];
                  firstRegion.attach(text, index);
                }
                text.focus();

                break;

              case 'image':
                var image = new ContentEdit.Image({
                  src: item.src,
                  width: item.width,
                  height: item.height
                });
                if (node.parent()) {
                  node.parent().attach(image, index);
                } else {
                  var firstRegion = app.orderedRegions()[0];
                  firstRegion.attach(image, index);
                }
                image.focus();

                break;
            }

            imageChooserDialog.dispose();
          };
        });

        imageChooserDialog.open();
        return callback(true);
      };

      return EWMedia;

    })(ContentTools.Tool);
  }
})();
