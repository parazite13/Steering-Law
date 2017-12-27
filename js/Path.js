function Path(){
	//crée l'arc vert de départ (constante pour tous les chemins)
	var arcStart = new Arc(1000, undefined, colorStart);
	arcStart.center = {x:50, y:canvas[0].height / 2 + arcStart.radius};
	arcStart.start = -Math.PI/2;
	arcStart.end = arcStart.start - Math.PI / 100;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(1000, undefined, colorEnd);
	arcEnd.center = arcStart.center;
	arcEnd.start = arcStart.start + Math.PI/100;
	arcEnd.end = arcStart.start;
	arcEnd.isTrigonometrique = true;
	this.arcs.push(arcEnd);

	this.draw = function(){
		$.each(this.arcs, function(index, arc){
			arc.draw();
		});
	}
	this.add = function(arc){
		//retire le dernier élément (arc de fin de chemin)
		this.arcs.splice(this.arcs.length - 1, 1);

		//ajoute celui souhaité (faites confiance aux calculs, ça a été posé sur papier.)
		var lastCurrentArc = this.arcs[this.arcs.length - 1];
		var xCenter = lastCurrentArc.center.x;
		var yCenter = lastCurrentArc.center.y;
		var xStart = lastCurrentArc.getStart().x;
		var yStart = lastCurrentArc.getStart().y;

		var angle = Math.atan((yCenter - yStart) / (xCenter - xStart));

		var xNewCenter;
		var yNewCenter;

		// postion du début de l'arc d'avant, incrémenté de 1 a chaque quart de cercle
		// dans le sens trigo du canvas
		// en bas a droite => 1, en bas a gauche => 2...
		var config;
		
		if(lastCurrentArc.getStart().x <= lastCurrentArc.center.x){
			if(lastCurrentArc.getStart().y < lastCurrentArc.center.y){
				config = 3;
			}else{
				config = 2;
			}
		}else{
			if(lastCurrentArc.getStart().y < lastCurrentArc.center.y){
				config = 4;
			}else{
				config = 1;
			}
		}

		switch(config){
			case 1:
				xNewCenter = xStart + arc.radius * Math.cos(angle);
				yNewCenter = yStart + arc.radius * Math.sin(angle);
				break;
			case 2:
				xNewCenter = xStart - arc.radius * Math.cos(angle);
				yNewCenter = yStart - arc.radius * Math.sin(angle);
				break;
			case 3:
				xNewCenter = xStart - arc.radius * Math.cos(angle);
				yNewCenter = yStart - arc.radius * Math.sin(angle);
				break;
			case 4:
				xNewCenter = xStart + arc.radius * Math.cos(angle);
				yNewCenter = yStart + arc.radius * Math.sin(angle);
				break;
		}
		
		var newCenter = {x:xNewCenter, y:yNewCenter};

		arc.center = newCenter;

		// Cas particulier pour le premier		
		if(this.arcs.length == 1){
			arc.end = Math.PI + angle;
			arc.start = arc.end - arc.angle;
			arc.isTrigonometrique = false;

		// Sinon c'est en fonction du sens trigo ou non de l'arc d'avant
		}else{
			if(lastCurrentArc.isTrigonometrique){
				// Et aussi en fonction de la config
				switch(config){
					case 1:
						console.log("A1");
						arc.end = Math.PI + angle;
						arc.start = arc.end - arc.angle;
						arc.isTrigonometrique = false;
						break;
					case 2:
						console.log("A2");
						arc.end = angle;
						arc.start = arc.end - arc.angle;
						arc.isTrigonometrique = false;
						break;
					case 3:
						console.log("A3");
						arc.end = angle;
						arc.start = arc.end - arc.angle;
						arc.isTrigonometrique = false;
						break;
					case 4:
						console.log("A4");
						arc.end = Math.PI + angle;
						arc.start = arc.end - arc.angle;
						arc.isTrigonometrique = false;
						break;
				}
			}else{
				switch(config){
					case 1:
						console.log("B1");
						arc.end = Math.PI + angle;
						arc.start = arc.end + arc.angle;
						arc.isTrigonometrique = true;
						break;
					case 2:
						console.log("B2");
						arc.end = angle;
						arc.start = arc.end + arc.angle;
						arc.isTrigonometrique = true;
						break;
					case 3:
						console.log("B3");
						arc.end = angle;
						arc.start = arc.end + arc.angle;
						arc.isTrigonometrique = true;
						break;
					case 4:
						console.log("B4");
						arc.end = Math.PI + angle;
						arc.start = arc.end + arc.angle;
						arc.isTrigonometrique = true;
						break;
				}
			}
		}

		this.arcs.push(arc);
		//recalcule le dernier arc et l'ajoute
		this.addArcEnd();
	}
	this.setArc = function(index, radius, angle){
		this.arcs[index].radius = radius;
		this.arcs[index].angle = angle;

		if(index % 2 == 1){
			this.arcs[index].start = this.arcs[index].end - this.arcs[index].angle;
		}else{
			this.arcs[index].start = this.arcs[index].end + this.arcs[index].angle;
		}

		//retire le dernier élémentpuis le rajoute (recalcule)
		this.arcs.splice(this.arcs.length - 1, 1);
		this.addArcEnd();

	}
	//fonction qui calcul un arc de fin selon le chemin courant et l'insert
	this.addArcEnd = function(){
		var lastCurrentArc = this.arcs[this.arcs.length - 1];
		var newEnd = new Arc(lastCurrentArc.radius, undefined, colorEnd);
		newEnd.center = lastCurrentArc.center;
		newEnd.end = lastCurrentArc.start;
		if(this.arcs.length % 2 == 1){
			newEnd.isTrigonometrique = true;
			newEnd.start = newEnd.end + Math.PI/(lastCurrentArc.radius / 10);
		}else{
			newEnd.isTrigonometrique = false;
			newEnd.start = newEnd.end - Math.PI/(lastCurrentArc.radius / 10);
		}
		this.arcs.push(newEnd);
	}
	//fonction qui change la couleur de tous les arcs du chemin (sauf départ et arrivée)
	this.setColor = function(color){
		var lastIndex = this.arcs.length - 1;
		$.each(this.arcs, function(index, arc){
			if(index != 0 && index != lastIndex){
				arc.color = color;
			}
		});
	}
}