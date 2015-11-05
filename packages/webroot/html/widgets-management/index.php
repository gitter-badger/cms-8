<?php
session_start();

function sidebar()
{
   ob_start();
   ?>
   <ul>        
      <li>      
         <a rel="ajax" data-default="true" data-ew-nav="uis-list" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/uis-list.php">      
            tr{Layouts}   
         </a>     
      </li>   
      <li>       
         <a rel="ajax" data-ew-nav="pages-uis" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/pages-uis.php">        
            tr{Layouts and Contents}        
         </a>        
      </li>    
      <li>         
         <a rel="ajax" data-ew-nav="widgets" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/widgets.php">         
            tr{Widgets Types}   
         </a>      
      </li>   
   </ul> 
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script>
      (function ()
      {
         var WidgetsManagement = System.module("widgets-management");

         WidgetsManagement.onInit = function ()
         {

         };

         WidgetsManagement.onStart = function ()
         {
            this.data.tab = null;           
         };
         
         WidgetsManagement.onActive = function ()
         {
         };

         WidgetsManagement.on("app", function (p, section)
         {
            if (!section || section === this.data.tab)
               return;
            this.data.tab = section;
            EW.appNav.setCurrentTab($("a[data-ew-nav='" + section + "']"));
         });


      }());
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);