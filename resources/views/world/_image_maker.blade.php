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

    <button id="save" class="btn btn-primary" onclick="exportCanvasAsPNG('characterCanvas','myomaker.png')">Save</button>
  </div>
  </canvas>
</div>

<script>
  const canvas = document.getElementById("characterCanvas");
  const ctx = canvas.getContext("2d");

  function hexToHSL(H) {
    // Convert hex to RGB first
    let r = 0,
      g = 0,
      b = 0;
    if (H.length == 4) {
      r = "0x" + H[1] + H[1];
      g = "0x" + H[2] + H[2];
      b = "0x" + H[3] + H[3];
    } else if (H.length == 7) {
      r = "0x" + H[1] + H[2];
      g = "0x" + H[3] + H[4];
      b = "0x" + H[5] + H[6];
    }
    // Then to HSL
    r /= 255;
    g /= 255;
    b /= 255;
    let cmin = Math.min(r, g, b),
      cmax = Math.max(r, g, b),
      delta = cmax - cmin,
      h = 0,
      s = 0,
      l = 0;

    if (delta == 0)
      h = 0;
    else if (cmax == r)
      h = ((g - b) / delta) % 6;
    else if (cmax == g)
      h = (b - r) / delta + 2;
    else
      h = (r - g) / delta + 4;

    h = Math.round(h * 60);

    if (h < 0)
      h += 360;

    l = (cmax + cmin) / 2;
    s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
    s = +(s * 100).toFixed(1);
    l = +(l * 100).toFixed(1);

    return {
      H: h,
      S: s,
      L: l
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

  function addImage(imagePath, hsl) {
    var img = new Image();
    img.src = imagePath;
    ctx.drawImage(render(img, hsl), 0, 0);
  }

  function refreshImage() {
    var selects = document.querySelectorAll("select");
    var colors = document.querySelectorAll("input[type=text]");
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i < selects.length; i++) {
      var hsl = hexToHSL(colors[i].value);
      addImage(selects[i].value, hsl);
    }

  }

  function render(img, color) {

    var canvasTemp = document.createElement("canvas");
    var ctxTemp = canvasTemp.getContext("2d");

    canvasTemp.width = 500;
    canvasTemp.height = 500;

    ctxTemp.clearRect(0, 0, canvas.width, canvas.height);
    ctxTemp.globalCompositeOperation = "source-over";
    ctxTemp.drawImage(img, 0, 0, canvas.width, canvas.height);

    var h = color.H;
    var s = color.S;
    var l = color.L;

    // adjust "lightness"
    ctxTemp.globalCompositeOperation = l < 100 ? "color-burn" : "color-dodge";
    // for common slider, to produce a valid value for both directions
    l = l >= 100 ? l - 100 : 100 - (100 - l);
    ctxTemp.fillStyle = "hsl(0, 50%, " + l + "%)";
    ctxTemp.fillRect(0, 0, canvas.width, canvas.height);

    // adjust saturation
    ctxTemp.globalCompositeOperation = "saturation";
    ctxTemp.fillStyle = "hsl(0," + s + "%, 50%)";
    ctxTemp.fillRect(0, 0, canvas.width, canvas.height);

    // adjust hue
    ctxTemp.globalCompositeOperation = "hue";
    ctxTemp.fillStyle = "hsl(" + h + ",1%, 50%)";
    ctxTemp.fillRect(0, 0, canvas.width, canvas.height);

    // clip
    ctxTemp.globalCompositeOperation = "destination-in";
    ctxTemp.drawImage(img, 0, 0, canvas.width, canvas.height);

    // reset comp. mode to default
    ctxTemp.globalCompositeOperation = "destination-over";
    return canvasTemp;
  }

  $(document).ready(function() {
    var colors = document.querySelectorAll("input[type=text]");
    for (var i = 0; i < colors.length; i++) {
      colors[i].value = "FFFFFF";
    }
    ctx.fillStyle = "white";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  });
</script>