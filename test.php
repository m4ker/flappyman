<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <style>
        body {
            margin: 0px;
            padding: 0px;
            text-align:center;
        }
        canvas {
            border:1px solid #000;
            margin:0 auto;
            background:skyblue;
        }
    </style>
</head>
<body>
<canvas id="myCanvas" width="400" height="600"></canvas>
<script>
    var canvas = document.getElementById('myCanvas');
    var context = canvas.getContext('2d');
    var imageObj = new Image();

    imageObj.onload = function() {
	
	context.drawImage(imageObj, 100, 100, 40, 40);
	context.save();
	context.translate(100,100);
	context.rotate(Math.PI/180*45);
	context.drawImage(imageObj, 0, 0, 40, 40);
	//context.rotate(-Math.PI/180*45);
	context.restore();
	context.drawImage(imageObj, 200, 200, 40, 40);

    };
    imageObj.src = '<?php echo $_GET['user'];?>.png';
    
</script>
</body>
</html>      