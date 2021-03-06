/* global System */

'use strict';

System.entity('state-handlers/widgets-management/page-layouts', PageLayoutsStateHandler);

/**
 * 
 * @param {System.MODULE_ABSTRACT} state
 */
function PageLayoutsStateHandler(state) {
  var handler = this;
  handler.state = state;
  handler.states = {};

  handler.state.onInit = function () {
    handler.init();
  };

  handler.state.onStart = function () {
    handler.start();
  };
}

PageLayoutsStateHandler.prototype.init = function () {
  this.state.on('app', System.utility.withHost(this.state).behave(System.services.app_service.select_sub_section));
};

PageLayoutsStateHandler.prototype.start = function () {
  System.entity('ui/app-bar').subSections = [
    {
      title: 'Contents Layouts',
      state: 'contents-layouts',
      url: 'html/webroot/widgets-management/contents-layouts-tab/component.php',
      id: 'widgets-management/pages-uis/contents-layouts'
    },
    {
      title: 'All Layouts',
      state: 'all',
      url: 'html/webroot/widgets-management/all-layouts-tab/component.php',
      id: 'widgets-management/pages-uis/all'
    }
  ];

  if (!System.entity('ui/apps').currentSubSection) {
    System.entity('ui/apps').goToState('widgets-management/pages-uis/contents-layouts');
  }
};

// ------ Registring the state handler ------ //

System.state('widgets-management/pages-uis', function (state) {
  new PageLayoutsStateHandler(state);
});