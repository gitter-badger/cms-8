<?php/* * title: List * description: Create a list of items or select a list feeder * feeder_type: list */?><div class="block-row mt">  <div class="col-xs-12 ">          <!--<input class="text-field" name="feeder" id="feeder" data-label="Folder, article or list feeder" data-ew-plugin="link-chooser" data-content-type="list">-->        <system-json-input data-ew-plugin="link-chooser" name="feeder" id="feeder" data-content-type="list">          </system-json-input>  </div>  <div class="btn-group col-xs-12 mt" data-toggle="buttons">    <label class="btn btn-primary btn-sm pull-right" >      <input type="checkbox" name="default-content" id="default-content" value="yes" > Default Content    </label>  </div></div><div class="block-row">  <div class="col-xs-12" >    <h2 class="ta-center">      Pagination Controls    </h2>    <div class="btn-group btn-group-justified" data-toggle="buttons">      <label class="btn btn-default">        <input type="checkbox" name="show_top_buttons" id="show_top_buttons" value="true" > tr{Show Header Pagination}      </label>      <label class="btn btn-default ">        <input type="checkbox" name="show_bottom_buttons" id="show_bottom_buttons" value="true"> tr{Show footer Pagination}      </label>            </div>  </div></div><div class="block-row">  <h2 class="ta-center">    Items Count  </h2>  <div class="block-row">    <input class="col-xs-12" type="text" name="items-count" id="items-count" value="4" data-slider="true" data-slider-range="1,36" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >  </div></div><div class="block-row">  <h2 class="ta-center">    Items Width  </h2>  <div class="col-lg-6 col-xs-12">    <div class="row">      <label class="ta-center">        Width large Screen      </label>              <input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >    </div>  </div>  <div class="col-lg-6 col-xs-12">    <div class="row">      <label class="ta-center">        width normal Screen      </label>      <input class="col-xs-12" type="text" name="col-md-" id="col-md-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >    </div>  </div>  <div class="col-lg-6 col-xs-12">    <div class="row">      <label class="ta-center">        Width tablet Screen      </label>      <input class="col-xs-12" type="text" name="col-sm-" id="col-sm-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >    </div>  </div>    <div class="col-lg-6 col-xs-12">    <div class="row">      <label class="ta-center">        Width mobile Screen      </label>      <input class="col-xs-12" type="text" name="col-xs-" id="col-xs-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >    </div>  </div></div><div class="block-row">  <div class="col-xs-12" >    <h2 class="ta-center">      Order By    </h2>    <div class="btn-group btn-group-justified" data-toggle="buttons">      <label class="btn btn-default">        <input type="radio" name="order" id="order" value="default"> tr{Default}      </label>      <label class="btn btn-default">        <input type="radio" name="order" id="order" value="ASC" > tr{Oldest}      </label>      <label class="btn btn-default ">        <input type="radio" name="order" id="order" value="DESC"> tr{Newest}      </label>            </div>  </div></div><div class="block-row">  <div class="col-xs-12" >    <h2 class="ta-center">      Extra    </h2>    <div class="btn-group btn-group-justified" data-toggle="buttons">      <label class="btn btn-default">        <input type="checkbox" name="hide_see_more" id="hide_see_more" value="true"> tr{Hide see more}      </label>        </div>  </div></div><div class="block-row">  <h2 class="ta-center">    tr{Content Fields}  </h2>  <ul id="widget-list-content-fields" class="list arrangeable fields">    <li class="" style="">      <div class="wrapper">        <div class="handle"></div>        <label>Field name</label>        <input class="text-field" name="content_fields"/>        </div>    </li>  </ul></div><script>  $("#uis-widget").on("refresh", function (e, data) {    $("#widget-list-content-fields").EW().dynamicList({      value: {        'content_fields': data['content_fields']      }    });  });</script>