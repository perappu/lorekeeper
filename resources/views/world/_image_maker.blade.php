@php

$categories = \App\Models\MYOMaker\MYOMakerCategory::orderBy('id')->get();

@endphp

<canvas id="characterCanvas" width="500" height="500">

</canvas>

@foreach ($categories as $category)
  <select id="{{$category->name}}" name="{{$category->name}}" onmousedown="this.value='';" onchange="refreshImage();">
    @php
    $images = \App\Models\MYOMaker\MYOMakerImage::where('category_id', $category->id)->get();
    @endphp

    @foreach ($images as $image)
      <option value='{{$image->imageUrl}}'>{{$image->name}}</option>
    @endforeach

  </select>
@endforeach

  <button id="save" onclick="exportCanvasAsPNG('characterCanvas','test.png')">Save</button>

<script>

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
        var c=document.getElementById("characterCanvas");
                var ctx=c.getContext("2d");
                var img=new Image();
                img.onload = function(){
                    ctx.drawImage(img,0,0);
                };
                img.src=imagePath;
                console.log(imagePath);
    }

    function refreshImage() {
        var selects = document.querySelectorAll("select");
        var canvas = document.getElementById("characterCanvas");
        const context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        for(var i = 0; i < selects.length; i++) {
            addImage(selects[i].value);
        }
    }
</script>