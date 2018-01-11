function Arc(radius, angle, color){
	this.radius = radius;
	this.angle = angle;
	this.center = {x:0, y:0};
	this.width = 80;
	this.start;
	this.end;
	this.isTrigonometrique;
	this.color = color;
	this.draw = function(){
		ctx.strokeStyle = "#000000";
		ctx.beginPath();
		ctx.lineWidth = this.width;
		ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		ctx.stroke();
		
		ctx.strokeStyle = this.color;
		ctx.beginPath();
		ctx.lineWidth = this.width - 4;
		ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		ctx.stroke();
	}
	this.getStart = function(){
		return {x:this.center.x + this.radius * Math.cos(this.start), y:this.center.y + this.radius * Math.sin(this.start)};
	}
	this.getEnd = function(){
		return {x:this.center.x + this.radius * Math.cos(this.end), y:this.center.y + this.radius * Math.sin(this.end)};
	}
}