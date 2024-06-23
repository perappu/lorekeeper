<canvas id="characterCanvas" width="500" height="500">

</canvas>

<select id="tail" name="tail" onmousedown="this.value='';" onchange="refreshImage();">
    <option value='1'>One</option>
    <option value='2'>Two</option>
  </select>

  <select id="head" name="head" onmousedown="this.value='';" onchange="refreshImage();">
    <option value='1'>One</option>
    <option value='2'>Two</option>
  </select>

  <select id="feet" name="feet" onmousedown="this.value='';" onchange="refreshImage();">
    <option value='1'>One</option>
    <option value='2'>Two</option>
  </select>

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
            addImage('images/' + selects[i].id + '/' + selects[i].value + '.png');
        }
    }
</script>