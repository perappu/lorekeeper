@php

$categories = \App\Models\MYOMaker\MYOMakerCategory::orderBy('order')->get();

@endphp

<style>
  canvas {
    padding-left: 0;
    padding-right: 0;
    margin-left: auto;
    margin-right: auto;
    display: block;
  }
</style>

<div class="row">
  <div class="col">
    <canvas id="characterCanvas" width="500" height="500"></canvas>
  </div>
  <div class="col">

    @foreach ($categories as $category)
    <div class="form-group">
      <label>{{$category->name}}</label>
      <select class="form-control" id="{{$category->name}}" name="{{$category->name}}" onmousedown="this.value='';" onchange="refreshImage();">
        @php
        $images = \App\Models\MYOMaker\MYOMakerImage::where('category_id', $category->id)->get();
        @endphp

        @foreach ($images as $image)
        <option value='{{$image->imageUrl}}'>{{$image->name}}</option>
        @endforeach

      </select>
      <div class="form-group">
        <div class="input-group cp">
          <input class="form-control" id="color{{$category->id}}" type="text" onchange="refreshImage();">
          <span class="input-group-append">
            <span class="input-group-text colorpicker-input-addon"><i></i></span>
          </span>
        </div>
      </div>
    </div>
    @endforeach

    <button id="save" class="btn btn-primary" onclick="exportCanvasAsPNG('characterCanvas','test.png')">Save</button>
  </div>
  </canvas>
</div>

<script>



  function HexToRGB(Hex) {
    var Long = parseInt(Hex.replace(/^#/, ""), 16);
    return {
      R: (Long >>> 16) & 0xff,
      G: (Long >>> 8) & 0xff,
      B: Long & 0xff
    };
  }

  function exportCanvasAsPNG(id, fileName) {

    var canvasElement = document.getElementById(id);

    var MIME_TYPE = "image/png";

    var imgURL = canvasElement.toDataURL(MIME_TYPE);

    var dlLink = document.createElement('a');
    dlLink.download = fileName;
    dlLink.href = imgURL;
    dlLink.dataset.downloadurl = [MIME_TYPE, dlLink.download, dlLink.href].join(':');

    document.body.appendChild(dlLink);
    dlLink.click();
    document.body.removeChild(dlLink);
  }

  function addImage(imagePath) {
    var c = document.getElementById("characterCanvas");
    var ctx = c.getContext("2d");
    var img = new Image();
    img.onload = function() {
      ctx.drawImage(img, 0, 0);
    };
    img.src = imagePath;
  }

  function refreshImage() {
    var selects = document.querySelectorAll("select");
    var colors = document.querySelectorAll("input[type=text]");
    var canvas = document.getElementById("characterCanvas");
    const context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
    for (var i = 0; i < selects.length; i++) {

      //var color = document.querySelector(`[data-colorpicker-id=['${i+1}']] input`);
      var color = colors[i].value;

      var rgb = HexToRGB(color);

      console.log(rgb);
      addImage(selects[i].value);
    }
  }


</script>