# p5.turtle.js

p5.jsのためのタートルグラフィックプログラム

これは、シンプルでアニメーションするタートルグラフィックプログラムです。処理は、2つのステップがあります。最初に、setup()の中で、タートルの動きを記録します。2番目に、draw()の中で、タートルの動きをアニメーションで表示します。このプログラムは、p5.jsとp5.play.jsライブラリを必要とします。


## デモ

http://ycatch.github.io/p5.turtle.js/


## 利用条件

Copyright 2015 - 2016 Yutaka Kachi released under the MIT license.

https://opensource.org/licenses/MIT


p5.js and p5.play.js is released under the LGPL2.1


## コマンド

- forward(length)		前進
- back(length)			後進
- left(angle in degree)		左回転
- right(angle in degree)	右回転


## Properties and default

- turtle.x = 200;			X座標
- turtle.y = 60;			Y座標
- turtle.step = 5;			アニメーション時の移動速度
- turtle.stepAngle = Math.PI / 36;	アニメーション時の回転速度
- turtle.angleInRadians = 0;		角度(ラジアン)
- turtle.penDown = false;		ペンで描く = true、描かない = fales
- turtle.penColor = "#000000";		ペンの色
- turtle.lineWidth = 2;			線の幅


## Colors

- turtle.color.black :		"#000000"	黒
- turtle.color.gray :		"#808080"	灰色
- turtle.color.lightgray :	"#C0C0C0"	明るい灰色
- turtle.color.red :		"#ff0000"	赤
- turtle.color.green :		"#00ff00"	緑
- turtle.color.blue :		"#0000ff"	青
- turtle.color.yellow :		"#ffff00"	黄色
- turtle.color.magenta :	"#ff00ff"	紫
- turtle.color.aqua :		"#00ffff"	水色
- turtle.color.white :		"#ffffff"	白


## p5.play.js

2016.10.27: p5.turtle.js use p5.play.js with #119 patch.
it needs for instance mode and createGraphics.

- PR: Fix default camera in instance mode #119
  https://github.com/molleindustria/p5.play/pull/119
- Issue: createGraphics does not work with p5.play in instance mode. #104
  https://github.com/molleindustria/p5.play/issues/104
