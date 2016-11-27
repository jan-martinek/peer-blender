/** Example of turtle graphics for p5.js. 
	Copyright 2015 Yutaka Kachi released under the MIT license.
 */

var turtles_path = []; // array of Turtle objects
var pathPointer = 0;
var turtle;
var turtleSprite;
var tPlane; // graphic plane for pen layer

setup = function() {
	var canvas = createCanvas(480, 360);
	canvas.parent("p5Canvas");
	background(200);

	turtleSprite = createSprite(0, 0, 56, 64);
	turtleSprite.addAnimation("moving", "turtle_1.png", "turtle_4.png");
	turtleSprite.changeAnimation("moving");
	
	tPlane = createGraphics(width, height); // pen layer
	
	// Start turtle code - recode turtle moving. -------------------------------------
	turtle = new Turtle();
	turtle.x = 140;
	turtle.y = 80;
	turtle.penDown = true;
	turtle.penColor = turtle.color.blue;
	for(var i = 0; i < 4; i++){
			turtle.forward(200);
			turtle.left(90);
	};
	turtle.right(360);
	turtle.right(360);
	// End of turtle code ------------------------------------------------------------
};

draw = function() {
	// Playback turtle moving for animation.
	background(200);
	turtle.draw2(pathPointer);
	image(tPlane);
	drawSprites();
	
	pathPointer += 1;
	if (pathPointer >= turtles_path.length) {
		pathPointer = 0;
		tPlane.fill(200);
		tPlane.noStroke();
		tPlane.rect(0, 0, width, height);
	}
};

/** Turtle Data */
function TBody() {
	this.x = 200;
	this.y = 60;
	this.step = 3;
	this.stepAngle = Math.PI / 36;
	this.angleInRadians = 0;
	this.penDown = false;
	this.penColor = "#000000";
	this.lineWidth = 2;
};

/** Turtle class */
function Turtle() {
	var body = new TBody();
	for (var prop in body) {
		this[prop] = body[prop];
	}
	
	this.color = {
		black : "#000000",
		gray: "#808080",
		lightgray: "#C0C0C0",
		red: "#ff0000",
		green: "#00ff00",
		blue: "#0000ff",
		yellow: "#ffff00",
		magenta: "#ff00ff",
		aqua: "#00ffff",
		white: "#ffffff"
	};

	this.forward = function(length) {
		var x0 = this.x;
		var y0 = this.y;
		var xx = sin(this.angleInRadians);
		var yy = cos(this.angleInRadians);
		var count = abs(int(length / this.step));
		var dir = 1;
		if(length < 0) {dir = -1};
		
		for(var i=0; i < count - 1; i++) {
			this.x += dir * this.step * xx;
			this.y += dir * this.step * yy;
			this.copy();			
		}
		this.x = x0 + length * xx;
		this.y = y0 + length * yy;
		this.copy();

	};
	
	this.back = function(length) {
		this.forward(-length);
	};
	
	this.left = function(angleInDegrees) {
		var angle0 = this.angleInRadians;
		var targetAngle = angleInDegrees * Math.PI / 180.0;
		var count = abs(int(targetAngle / this.stepAngle));
		var dir = 1;
		if(targetAngle < 0) {dir = -1};
		
		for(var i=0; i < count - 1; i++) {
			this.angleInRadians += dir * this.stepAngle;
			this.copy();
		}
		this.angleInRadians = angle0 + targetAngle;
		this.copy();
	};
	
	this.right = function(angleInDegrees) {
		this.left(-angleInDegrees);
	};

	// copy TBody object
	this.copy = function() {
		turtles_path.push(new TBody());
		var target = turtles_path[turtles_path.length - 1];
		for (var prop in this) {
			target[prop] = this[prop];
		}
	};
	
	// drawing turtle in loop
	this.draw2 = function(pointer) {
		var target = turtles_path[pointer];
		
		// draw path by Pen
		if (target.penDown) {
			tPlane.strokeWeight(target.lineWidth);
			tPlane.stroke(target.penColor);
			var nextPointer = pointer + 1;
			if(nextPointer >= turtles_path.length) {
				nextPointer = 0;
			}
			tPlane.line(target.x, target.y, turtles_path[nextPointer].x, turtles_path[nextPointer].y);
		}
		
		// draw turtle by sprite
		turtleSprite.rotation = target.angleInRadians * -180 / Math.PI + 180;
		turtleSprite.position.x = target.x;
		turtleSprite.position.y = target.y;
	};
};