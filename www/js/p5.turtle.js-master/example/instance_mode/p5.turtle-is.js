/** p5.turtle-is.js
 Copyright 2015 Yutaka Catch.

 instance mode and draw static.
 release under the MIT License.
 **/

var Pjs;

var s = function(p) {
	var turtles_path = [];
	var pathPointer = 0;
	var turtle;
	var turtleSprite;
	var tPlane;

	p.setup = function() {
		p.createCanvas(480, 360);
		p.background(200);
		p.fill(255);

		tPlane = p.createGraphics(p.width, p.height);
		
		// Start turtle code - recode turtle moving. -------------------------------------
		turtle = new p.Turtle();
		turtle.x = 200;
		turtle.y = 80;
		turtle.penDown = true;
		turtle.penColor = turtle.color.blue;

		for(var i = 0; i < 5; i++){
			turtle.forward(200);
			turtle.left(144);
		};
		// End of turtle code ------------------------------------------------------------
	};

	p.TBody = function() {
		this.x = 200;
		this.y = 60;
		this.step = 3;
		this.stepAngle = Math.PI / 36;
		this.angleInRadians = 0;
		this.penDown = false;
		this.penColor = "#000000";
		this.lineWidth = 2;
	};

	p.Turtle = function() {
		var body = new p.TBody();
		for (var prop in body) {
			this[prop] = body[prop];
		};

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
			var xx = Math.sin(this.angleInRadians);
			var yy = Math.cos(this.angleInRadians);
			this.x = x0 + length * xx;
			this.y = y0 + length * yy;
			
			if (this.penDown) {
				p.stroke(this.penColor);
				p.strokeWeight(this.lineWidth);
				p.line(this.x, this.y, x0, y0);
			}
		};
		
		this.back = function(length) {
			this.forward(-length);
		};
		
		this.left = function(angleInDegrees) {
			var angle0 = this.angleInRadians;
			var targetAngle = angleInDegrees * Math.PI / 180.0;
			this.angleInRadians = angle0 + targetAngle;
			if(targetAngle >= Math.PI) {
				targetAngle -= Math.PI;
			}
		};
		
		this.right = function(angleInDegrees) {
			this.left(-angleInDegrees);
		};
	};

};

Pjs = new p5(s, "p5Canvas");