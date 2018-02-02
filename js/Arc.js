function Arc(radius, angle, color, ctx){
	this.ctx = ctx;
	this.radius = radius;
	this.angle = angle;
	this.center = {x:0, y:0};
	this.width = 80;
	this.start;
	this.end;
	this.isTrigonometrique;
	this.color = color;
	this.draw = function(){
		this.ctx.strokeStyle = this.color;
		this.ctx.beginPath();
		this.ctx.lineWidth = this.width - 4;
		this.ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		this.ctx.stroke();
	}
	this.drawBorder = function(){
		this.ctx.strokeStyle = "#000000";
		this.ctx.beginPath();
		this.ctx.lineWidth = this.width;
		this.ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		this.ctx.stroke();
	}
	this.getStart = function(){
		return {x:this.center.x + this.radius * Math.cos(this.start), y:this.center.y + this.radius * Math.sin(this.start)};
	}
	this.getEnd = function(){
		return {x:this.center.x + this.radius * Math.cos(this.end), y:this.center.y + this.radius * Math.sin(this.end)};
	}
}