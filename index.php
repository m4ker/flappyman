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
    var cw = 400;
    var ch = 600;

    // frame time 每帧时长
    var ft = 20;
    // 地心引力
    var g = 9.8;
    // 飞行初速度（向上）
    var v0 = 8; // 10
    // 水平飞行速度
    var v = 0.12;
    // 长度换算比例
    var scale = 1/30;
    // interval句柄
    var timer;

    var state = 0;// 0 init, 1=> gameing, 2 pause, 3 gameover
    // 初始坐标
    var x=100;
    var y=280;
    // 当前坐标
    var cx = 0;
    var cy = 0;
    // 运动时间
    var t=0;
    // 得分计数器
    var score = 0;
    // 距离计数器
    var distance = 0;
    // 点击计数器
    var click = 0;
    var wall = [];
    // 上一个墙生成的距离
    var last_wall = 0;

    var canvas = document.getElementById('myCanvas');
    var context = canvas.getContext('2d');
    var imageObj = new Image();

    imageObj.onload = function() {
        timer = setInterval(function(){
            if (state === 0) {
                // clear 
                context.clearRect(0, 0, canvas.width, canvas.height);
                // 计算当前上抛时间
                t += ft;
                // 计算当前位移
                h = Math.sin(t/200);
                // 计算当前位置
                cx = x;
                cy = y-(h/scale); // 偏移像素
                
                draw_photo(cx,cy);
            } else if (state ===1) {
                // clear 
                context.clearRect(0, 0, canvas.width, canvas.height);
                // 计算当前上抛时间
                t += ft;
                // 计算当前位移
                h = (v0 * (t/1000)) - ((g*(t/1000)*(t/1000))/2);
                
                
                hy = f_h(v0,g);// 顶点y
                hx = f_t(v0, g, v/scale);// 顶点x
                k = f_k((t/1000)*v/scale, h, hx, hy);// 斜率
                r = Math.atan(k); // 弧度
                
                // 计算当前位置
                cx = x;
                cy = y-(h/scale); // 偏移像素

                if (wall.length > 0) {
                    // clear wall
                    var shift = false;
                    for ( i in wall ) {
                        if (wall[i].x + wall[i].width < 0) {
                            shift = true;
                            continue;
                        }
                        if (wall[i].x < x && wall[i].score === 0) {
                          wall[i].score = 1;
                          score ++;
                        }
                        draw_wall(wall[i].x, wall[i].y, wall[i].width, wall[i].height);
                        wall[i].x -= v/scale;
                    }
                    if (shift)
                        wall.shift();
                }
                draw_photo(cx, cy, r);
                // 碰撞检测
                if ((cy+40) > ch) {
                    game_over();
                }
                if (wall.length > 0) {
                    for ( i in wall ) {
                        if (is_collide(cx,cy,40,40,wall[i].x,wall[i].y,wall[i].width,wall[i].height)) {
                            game_over();
                        }
                    }
                }

                new_wall();
                distance += v/scale;
                last_wall += v/scale;
            } else if (state === 2) {
                // 暂停
            } else if (state === 3) {
                // 游戏结束
            }
            draw_score();
        }, ft);

        // 鼠标点击重置上抛参数
        document.addEventListener('click', function() {
            if (state === 0 ) {
                state = 1;
                t = 0;
                x = cx;
                y = cy;
            } else if (state === 1) {
                click++;
                // 飞行高度限制
                if (cy<0)
                    return;
                t = 0;
                x = cx;
                y = cy;
            } else if (state === 2) {
            } else if (state === 3) {
              init();
            }
        });

    };
    imageObj.src = '<?php echo $_GET['user'];?>.png';
    
    function f_h(v0,g) {
      return (v0*v0) / (2*g);
    }
    
    function f_t(v0, g, s) {
    	return v0/g * s;
    }
    
    function f_k(x,y,x1,y1) {
      return (y1-y) * 2/(x1-x);
    }

    function draw_photo(x,y,rotate) {
        context.save();
        context.translate(x+20,y+20);
        context.rotate(-rotate);
        context.translate(-(x+20),-(y+20));
        context.drawImage(imageObj, x, y, 40, 40);
        context.restore();
    }
    
    function draw_score() {
      context.font = '40pt Calibri';
      context.fillStyle = 'blue';
      context.fillText(score, 200, 100);
    }

    function new_wall() {
        r = Math.random() * 100 ;
        if (r > 97 && last_wall > 200) { // todo:间距应该和头像尺寸有关
            wheight = 150+(Math.random()*200);
            wall.push({
                x:cw,
                y:(Math.random() > 0.5) ? (ch-wheight) : 0,
                height:wheight,
                width:80,
                score:0
            });
            last_wall = 0;
        }
    }

    function draw_wall (x,y,w,h) {
        context.beginPath();
        context.rect(x,y,w,h);
        context.fillStyle = 'yellow';
        context.fill();
        context.lineWidth = 7;
        context.strokeStyle = 'black';
        context.stroke();
    }

    function is_collide( RectX, RectY, RectWidth, RectHeight, ObjX, ObjY, ObjWidth, ObjHeight){
        if((RectX+RectWidth>ObjX)&&(RectX<ObjX+ObjWidth)&&
            (RectY+RectHeight>ObjY)&&(RectY<ObjY+ObjHeight))
            return true;//true表示两个矩形发生了碰撞
        return false;
    }

    function init() {
        state = 0;// 0 init, 1=> gameing, 2 pause, 3 gameover
        // 初始坐标
        //x=100;
        y=280;
        // 当前坐标
        cx = 0;
        cy = 0;
        // 运动时间
        t=0;
        // 得分计数器
        score = 0;
        // 距离计数器
        distance = 0;
        // 点击计数器
        click = 0;
        wall = [];
        // 上一个墙生成的距离
        last_wall = 0;
    }

    function game_over() {
        state = 3;
    }
</script>
</body>
</html>      