<div  class="row">
   <div class="elements-list" id="categories-list"  >

   </div>
</div>
<div class="row">
   <div class="elements-list"  id="articles-list">

   </div>
</div>

<script>
   (function (System) {
      function Documents(module) {
         var component = this;
         this.module = module;
         this.module.type = "app-section";

         this.module.onInit = function () {
            component.init();
         };

         this.module.onStart = function () {
            component.start();
         };
      }

      Documents.prototype.init = function () {
         var component = this;
         this.module.on("article", function (full, id) {
            if (!id) {
               component.bSee.comeOut();
               component.currentItem.removeClass("selected");
            }

            if (id) {
               component.bSee.comeIn();
            }
         });

         this.module.on("folder", function (full, id, command) {
            if (!id) {
               component.bSee.comeOut();
               component.currentItem.removeClass("selected");
            }

            if (id) {
               component.bSee.comeIn();
            }
         });

         this.module.on("dir", function (p, id, list) {
            if (!id) {
               id = "0";
            } else {
               component.bNewFolder.comeIn();
            }

            if (list) {
               if (component.parentId !== id) {
                  component.preParentId = component.parentId;
                  component.parentId = parseInt(id);

                  component.listCategories();
               }

               if (id === "0") {
                  component.bUp.comeOut(300);
               }

               if (id > 0) {
                  component.bUp.comeIn(300);
               }
            }
         });
      };

      Documents.prototype.start = function () {
         var _this = this;
         this.parentId = null;
         this.folderId = 0;
         this.articleId = 0;
         this.upParentId = 0;
         this.currentItem = $();
         this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {
            display: "none"
         });

         this.bUp.css("float", "right");
         this.bNewFolder = EW.addActivity({
            title: "tr{New Folder}",
            activity: "admin/html/content-management/folder-form.php",
            parent: "action-bar-items",
            hash: {
               folderId: null
            }
         }).hide();

         this.bNewFile = EW.addActivity({
            title: "tr{New Article}",
            activity: "admin/html/content-management/article-form.php_new",
            parent: "action-bar-items",
            hash: {
               articleId: null
            },
            onDone: function (hash) {
               hash.articleId = null;
            }
         }).hide();

         this.seeFolderActivity = EW.getActivity({
            activity: "admin/html/content-management/folder-form.php_see",
            onDone: function () {
               EW.setHashParameters({
                  folderId: null,
                  articleId: null
               });
            }
         });

         this.seeArticleActivity = EW.getActivity({
            activity: "admin/html/content-management/article-form.php_see",
            onDone: function () {
               EW.setHashParameters({
                  folderId: null,
                  articleId: null
               });
            }
         });

         if (this.seeArticleActivity || this.seeFolderActivity)
            this.bSee = EW.addAction("tr{See}", $.proxy(this.seeDetails, this), null, "action-bar-items").hide();
         else
            this.bSee = $();

         $(document).off("article-list");
         $(document).on("article-list.refresh", function (e, eventData) {
            _this.listCategories();
            if (eventData) {
               if (eventData.data.type === "article") {
                  EW.setHashParameters({
                     folderId: null,
                     articleId: eventData.data.id
                  }, "document");
               }

               if (eventData.data.type === "folder") {
                  EW.setHashParameters({
                     folderId: eventData.data.id,
                     articleId: null
                  }, "document");
               }
            }
         });

         this.bNewFile.comeIn();
         this.bNewFolder.comeIn();

         this.module.setParamIfNone("dir", "0/list");
      };

      Documents.prototype.preCategory = function () {
         this.module.setParam("dir", this.upParentId + "/list");
      };

      Documents.prototype.seeDetails = function () {
         var tFolderId = System.getHashParam("folder");
         var tArticleId = System.getHashParam("article");
         EW.activeElement = this.currentItem;
         if (tFolderId) {
            this.folderId = tFolderId;
            this.seeFolderActivity({
               folderId: tFolderId
            });
         } else if (tArticleId) {
            this.articleId = tArticleId;
            this.seeArticleActivity({
               articleId: tArticleId
            });
         }
      };

      Documents.prototype.listCategories = function () {
         var _this = this,
                 pId = 0,
                 hasNode = false,
                 article = System.getHashParam("article"),
                 folder = System.getHashParam("folder");
         /*lockFolders = System.UI.lock({
          element: $("#categories-list")[0],
          akcent: "loader top"
          }, .3),
          lockArticles = System.UI.lock({
          element: $("#articles-list")[0],
          akcent: "loader top"
          }, .3);*/

         $("#categories-list").html("<h2 >Loading Folders</h2><div class='loader center'></div>");
         System.addActiveRequest($.get('~admin/api/content-management/contents-folders', {
            parent_id: _this.parentId
         }, function (data) {
            $("#categories-list").html("<h2 id='cate-title'>tr{Folders}</h2><div class='box-content anim-fade-in'></div>");
            //$("#cate-title").loadingText();

            var foldersPane = $("#categories-list .box-content");
            $.each(data.data, function (index, element) {
               pId = element.up_parent_id;
               hasNode = true;
               var temp = _this.createFolderElement(element.title, element.round_date_created, element.id, element);
               //temp.addClass("anim-scale-in");
               if (element.id == folder) {
                  temp.addClass("selected");
                  _this.currentItem = temp;
               }
               foldersPane.append(temp);
               //temp.addClass("in");
            });

            if (hasNode) {
               _this.upParentId = pId;
            }
            $("#categories-list").find(".box-content").addClass("in");
            //lockFolders.dispose();
         }, "json"));

         $("#articles-list").html("<h2>Loading Article</h2><div class='loader center'></div>");
         System.addActiveRequest($.get('~admin/api/content-management/contents-articles', {
            parent_id: _this.parentId
         }, function (data) {
            $("#articles-list").html("<h2>tr{Articles}</h2><div class='box-content anim-fade-in'></div>");

            var articlesPane = $("#articles-list .box-content");
            $.each(data.data, function (index, element) {
               pId = element.up_parent_id;
               hasNode = true;
               var temp = _this.createArticleElement(element.title, element.round_date_created, element.id, element);
               //temp.addClass("anim-scale-in");
               if (element.id == article) {
                  temp.addClass("selected");
                  _this.currentItem = temp;
               }
               articlesPane.append(temp);
               // setTimeout(function ()            {
               //temp.addClass("in");
               //}, 1);

            });

            if (hasNode) {
               _this.upParentId = pId;
            }
            $("#articles-list").find(".box-content").addClass("in");
            //lockArticles.dispose();
         }, "json"));
      };

      Documents.prototype.focusOn = function (item) {
         if (this.currentItem) {
            this.currentItem.removeClass("selected");
         }
         item.addClass("selected");
         this.currentItem = item;
      };

      Documents.prototype.createFolderElement = function (title, dateCreated, id, model) {
         var self = this;
         var div = $("<div tabindex='1' class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.dblclick(function () {
            self.module.setParam("dir", id + "/list");
         });

         div.on('focus', function () {
            System.setHashParameters({
               article: null,
               folder: id
            });
            self.focusOn(div);
         });

         div.data("label", title);
         return div;
      };

      Documents.prototype.createArticleElement = function (title, dateCreated, id, model) {
         var self = this;
         var div = $("<div tabindex='1' class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.dblclick(function () {
            self.seeArticleActivity({
               articleId: id
            });
         });

         div.on('focus', function () {
            System.setHashParameters({
               folder: null,
               article: id
            });
            self.focusOn(div);
         });
         div.data("label", title);
         return div;
      };

      var module = function () {
         new Documents(this);
      };

      System.module("content-management").module("documents", module);
   }(System));
</script>