# p5.turtle.js

Example of turtle graphics for p5.js.

This is very simple and animated turtle graphics program. There are two step in the process. 1st step, it record turtle moving in setup(). 2nd step, it playback turtle moving for animation in draw(). This program needs p5.js and p5.play.js library.


## Demo

http://ycatch.github.io/p5.turtle.js/


## License

Copyright 2015 - 2016 Yutaka Kachi released under the MIT license.

https://opensource.org/licenses/MIT


p5.js and p5.play.js is released under the LGPL2.1


## Commands

- turtle.forward(length)
- turtle.back(length)
- turtle.left(angle in degree)
- turtle.right(angle in degree)


## Properties and default

- turtle.x = 200;
- turtle.y = 60;
- turtle.step = 5;
- turtle.stepAngle = Math.PI / 36;
- turtle.angleInRadians = 0;
- turtle.penDown = false;
- turtle.penColor = "#000000";
- turtle.lineWidth = 2;


## Colors

- turtle.color.black : "#000000"
- turtle.color.gray: "#808080"
- turtle.color.lightgray: "#C0C0C0"
- turtle.color.red: "#ff0000"
- turtle.color.green: "#00ff00"
- turtle.color.blue: "#0000ff"
- turtle.color.yellow: "#ffff00"
- turtle.color.magenta: "#ff00ff"
- turtle.color.aqua: "#00ffff"
- turtle.color.white: "#ffffff"


## p5.play.js

2016.10.27: p5.turtle.js use p5.play.js with #119 patch.
it needs for instance mode and createGraphics.

- PR: Fix default camera in instance mode #119
  https://github.com/molleindustria/p5.play/pull/119
- Issue: createGraphics does not work with p5.play in instance mode. #104
  https://github.com/molleindustria/p5.play/issues/104
