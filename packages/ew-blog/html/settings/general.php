<system-ui-view name="ew-blog-settings-card" class="card z-index-1 card-medium">
  <div class='card-header'>
    <h1>EW Blog</h1>
  </div>

  <div class='card-content'>
    <h3>Comments Feature</h3>

    <system-field class="field">
      <label>tr{Default comments feature status}</label>
      <select class="text-field" id="ew-blog/comments-feature" name="ew-blog/comments-feature" value="">
        <option v-for="option in options" v-bind:value="option.value">{{ option.title }}</option>
      </select>
    </system-field>
  </div>
</system-ui-view>

<script>
  new Vue({
    el: Scope.views.ew_blog_settings_card,
    data: {
      options: [
        {
          value: 1,
          title: 'Only confirmed comments'
        },
        {
          value: 2,
          title: 'All'
        },
        {
          value: -1,
          title: 'None, disable comments'
        }
      ]
    }
  });
</script>