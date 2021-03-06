<system-ui-view name="update-settings-card" class="card z-index-1 card-medium">
  <div class='card-header'>
    <div class="card-title-action"></div>
    <div class="card-title-action-right"></div>
    <h1>
      EW Admin
    </h1>
  </div>

  <div class='card-content'>
    <h2 class="">Current version: <span id="current-version">v0.9.0</span></h2>
    <h2 class="">Latest: <span id="latest-version"></span></h2>
    <button type="button" class="btn btn-default" v-on:click="checkForUpdate()">
      Check for updates
    </button>
    <button id="update-to-latest-btn" type="button" class="btn btn-success" style="display: none"
            v-on:click="update()">
      Update
    </button>
  </div>
</system-ui-view>

<script>
  var update_settings_card_vue = new Vue({
    el: Scope.views.update_settings_card,
    data: {},
    methods: {
      checkForUpdate: function () {
        $.get("http://api.github.com/repos/Eeliya/EverythingWidget/releases", function (response) {
          console.log(response);
          $("#latest-version").text(response[0].tag_name);
          if (response[0].tag_name > "v0.9.0") {
            $("#update-to-latest-btn").comeIn();
          }
        });
      },
      update: function () {
        alert("Updating functionality is not implemented yet");
      }
    }
  });
</script>