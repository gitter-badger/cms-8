<?php
if (!$is_allowed) {
  echo '<p> Commenting is disabled </p>';
  return;
}

?>

<form v-on:submit="postComment">
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

  <div class="field recaptcha">
    <div id="g-recaptcha-$php.widget_id" class="g-recaptcha"></div>
  </div>

  <div class="field actions">
    <button class="btn btn-submit" type="submit" v-bind:disabled="disablePosting" disabled="true">Post</button>
  </div>
</form>


<script >
  window.addEventListener('load', function () {
    var siteKey = '<?=
EWCore::call_api('admin/api/settings/read-settings', [
    'app_name' => 'webroot/google/recaptcha/site-key'
])['data']['webroot/google/recaptcha/site-key'];
?>';

    window.post_comment_$php.widget_id_js = function () {
      if (siteKey === '') {
        return postCommentForm.disablePosting = false;
      } else {
        postCommentForm.disablePosting = true;
      }

      grecaptcha.render('g-recaptcha-$php.widget_id', {
        sitekey: siteKey,
        callback: postCommentForm.verifyCapcha,
        'expired-callback': function () {
          grecaptcha.reset();
          postCommentForm.disablePosting = true;
        }
      });
    };


    var postCommentForm = new Vue({
      el: '[data-widget-id="$php.widget_id"]',
      data: {
        name: '',
        email: '',
        commenter_id: '',
        content: '',
        content_id: '<?= $content_id ?>',
        recaptcha: null,
        disablePosting: true
      },
      methods: {
        verifyCapcha: function (captcha) {
          $.ajax({
            type: 'POST',
            url: 'api/ew-blog/comments/confirm-capcha',
            data: {
              response: captcha
            },
            success: success
          });

          function success(response) {
            if (response.data.success) {
              postCommentForm.disablePosting = false;
            } else {
              grecaptcha.reset();
              alert('Error: ' + response.data['error-codes'].join(', '));
            }
          }
        },
        postComment: function (event) {
          event.preventDefault();
          var _this = this;

          if (!_this.$data.content_id) {
            return console.error('No `content_id` is specified for this comment widget');
          }

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
              window.location.reload();
            }
          }
        }
      }
    });

    (function (d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id))
        return;
      js = d.createElement(s);
      js.id = id;
      js.src = "https://www.google.com/recaptcha/api.js?onload=post_comment_$php.widget_id_js&render=explicit";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'google-recaptcha'));
  });
</script>
