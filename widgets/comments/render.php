<form v-on:submit="postComment">
  <h2 class="title">
    Post Comment
  </h2>

  <label class="field field-name">
    <input name="name" v-model="name"/>
    <span class="field-label">Name</span>
  </label>

  <label class="field field-email">
    <input name="email" v-model="email" required/>
    <span class="field-label">E-Mail</span>
  </label>

  <label class="field field-content">
    <textarea name="content" v-model="content" required></textarea>
    <span class="field-label">Comment</span>
  </label>

  <div class="field">
    <div class="g-recaptcha" data-sitekey="6LdgtigTAAAAADXq74JY8m5RIY7jDm9iidCbl7PM"></div>
  </div>
  <div>
    <button type="submit">Post</button>
  </div>
</form>

<script src='https://www.google.com/recaptcha/api.js'></script>
<script>

  (function () {
    var postCommentForm = new Vue({
      el: '[data-widget-id="{$widget_id}"]',
      data: {
        name: '',
        email: '',
        commenter_id: '',
        content: '',
        content_id: '<?= $_REQUEST['_method_name'] ?>',
        recaptcha: null
      },
      methods: {
        postComment: function (event) {
          event.preventDefault();
          var _this = this;
          _this.$data.recaptcha = grecaptcha.getResponse();

          $.ajax({
            type: 'POST',
            url: 'api/ew-blog/comments/',
            data: _this.$data,
            success: success
          });

          function success(response) {
            if (response.status_code === 200) {
              _this.$data.name = '';
              _this.$data.email = '';
              _this.$data.content = '';
            }
//            console.log(response, postCommentForm);
          }
          //console.log(<?= json_encode($_REQUEST) ?>,_this)
        }
      }
    });
  })();

</script>