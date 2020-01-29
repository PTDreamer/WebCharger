var temperature = 0.1;
var ABSOLUTE_ZERO = 1e-4;
var COOLING_RATE = 0.999999;
var CITIES = 50;
var current = [];
var best = [];
var best_cost = 0;

// var batteries = [
// {id:'1', value : 1020},
// {id:'2', value : 1130},
// {id:'3', value : 900},
// {id:'4', value : 1199},
// {id:'5', value : 1210},
// {id:'6', value : 909},
// {id:'7', value : 1103},
// {id:'8', value : 1300},
// {id:'9', value : 2},
// {id:'10', value : 3},
// {id:'11', value : 5},
// {id:'13', value : 1}];
var batteries = [
{id:'1', value : 1},
{id:'2', value : 2},
{id:'3', value : 3},
{id:'4', value : 4},
{id:'5', value : 4}];
var pack = [];
var bestPack = [];
var bestPDivergence = [];
var bestPDeviation = [];
var bestPsums = [];
var tempPack = [];
var packS;
var packP;
var packAvg;
var bestPackAvg;
var hasExtra = false;
var percentage = 0;
var initTemp = 0;
function copyPack(source, to) {
	for (var i = source.length - 1; i >= 0; i--) {
		to[i].bat.id = source[i].bat.id;
		to[i].bat.value = source[i].bat.value;
		to[i].s = source[i].s;
		to[i].p = source[i].p;
		to[i].extra = source[i].extra;
	}
}						 
function createPack(batList, s, p) {
	pack.length = 0;
	packP = p;
	packS = s;
	var ss = 1;
	var pp = 1;
	var extra = false;
	var cap = 0;
	var i = batList.length;
	while(i--) {
		if(extra)
			hasExtra = true;
		var obj = {bat : batList[i], s :ss, p : pp, extra : extra };
		pack.push(obj);
		cap = cap + batList[i].value;
		if ((pp == p) && (ss == s)){
				extra = true;
				pp = 0;
				ss = 0;
		}
		if(!extra) {
			if(ss == s) {
				ss = 1;
				pp = pp + 1;
			}
			else if(!extra){
				ss = ss + 1;
			}
		}
	}
	packAvg = (cap / (batList.length )* p);
}
function createNewPack(newPack) {
	var ok = true;
	do {
		ok = true;
		var i = Math.floor(Math.random() * (newPack.length));
		var ii =Math.floor(Math.random() * (newPack.length));
		if(i == ii) {
			ok = false;
			continue;
		}
		if((newPack[i].extra == true) && (newPack[ii].extra == true)) {
			ok = false;
			continue;
		}
		if(newPack[i].bat.value == newPack[ii].bat.value) {
			ok = false;
			continue;
		}
		if(newPack[i].s == newPack[ii].s) {
			ok = false;
			continue;
		}
		if(ok) {
			var val1 = newPack[i].bat.value;
			var id1 =newPack[i].bat.id;
			newPack[i].bat.value = newPack[ii].bat.value;
			newPack[i].bat.id = newPack[ii].bat.id;
			newPack[ii].bat.value = val1;
			newPack[ii].bat.id = id1;
		}
	} while (!ok);
}

function getError(p, pSums, pdivergence, pdeviation) {
	var error = 0;
	var tot = 0;
	var e = p.length;
	if(hasExtra) {
		while(e--) {
			if((!p[e].extra ==true)) {
				tot = tot + p[e].bat.value;
			}
		}
		packAvg = tot / packS;
	}
	for (i = 1; i < (packS + 1); i++) {
		var pSum = 0;
		var deviation = 0;
		for(ii = 1; ii < (packP + 1); ++ii) {
			var found = 0;
			for(iii = 0; iii < p.length -1; ++ iii) {
				if(p[iii].p == ii && p[iii].s == i) {
					found = iii;
					break;
				}
			}
			pSum = pSum + p[iii].bat.value;
			deviation = deviation + Math.abs(packAvg / packP - p[iii].bat.value);
		}
		pdeviation[i] = deviation / packP;
		pSums[i] = pSum;
		pdivergence[i] = Math.abs(pSum - packAvg);
		error = error + Math.abs(pSum - packAvg);
	}
	return error;
}

function getBestOf(batList, length) {
	var ret = []; 
	var temp = clone(batteries);
	var used = [];
	do {
		var bestVal = 0;
		var bestKey = null;
		var bestObj = {};
		var bestIndex = 0;
		var i = temp.length;
		while(i--)
		{
			if((temp[i].value > bestVal) && (used.indexOf(i) === -1)) {
				bestVal = temp[i].value;
				bestId = temp[i].id;
				bestIndex = i;
				bestObj = temp[i];
			}
		};
		used.push(bestIndex);
		ret.push(bestObj);
	} 
	while (ret.length < length);
	return ret;

} 
$(document).ready(function()
	{
		$("#info").hide();
		$("#solve").click(function()
			{
				temperature = parseFloat($("#temperature").val());
				initTemp = temperature;
				ABSOLUTE_ZERO = parseFloat($("#abszero").val());
				COOLING_RATE = parseFloat($("#coolrate").val());
				CITIES = parseInt($("#cities").val());
				init(2,2, false);
			});
	});

var tsp_canvas = document.getElementById('tsp-canvas');
var tsp_ctx = tsp_canvas.getContext("2d");

function deep_copy_array(array, to)
{
	var i = array.length;
	while(i--)
	{
		to[i] = array[i];
	}
}

function acceptanceProbability(current_cost, neighbor_cost)
{
	if(neighbor_cost < current_cost)
		return 1;
	return Math.exp((current_cost - neighbor_cost)/temperature);
}
var solverInt;
var painterInt;

function init(s, p, largestCap) {
	$("#info").hide();
	percentage = 0;
	if(largestCap)
		batteries = getBestOf(batteries, s*p);
	createPack(batteries, s, p);
	bestPack = clone(pack);
	console.log(pack);

	tempPack = clone(pack);
	//copyPack(pack, bestPack);
	console.log(bestPack);

	best_cost = getError(bestPack, [], [], []);
	solverInt = setInterval(solve, 1);
	painterInt = setInterval(paint, 1000);
	paint();
}

function solve()
{
	var psums = [];
	var perrors = [];
	var pdeviation = [];
	if(temperature>ABSOLUTE_ZERO)
	{
		var current_cost = getError(pack, psums, perrors, pdeviation);
		createNewPack(tempPack);
		var neighbor_cost = getError(tempPack, psums, perrors, pdeviation);
		//console.log("neighbor_cost:" + neighbor_cost);
		if(Math.random() < acceptanceProbability(current_cost, neighbor_cost))
		{
			//console.log("COPY TEMP TO PACK");
			copyPack(tempPack, pack);
			current_cost = getError(pack, psums, perrors, pdeviation);
		}
		if(current_cost < best_cost)
		{
			//console.log("COPY PACK TO BESTPACK");
			copyPack(pack, bestPack);
			deep_copy_array(psums, bestPsums);
			deep_copy_array(perrors, bestPDivergence);
			deep_copy_array(pdeviation, bestPDeviation);
			best_cost = current_cost;
			bestPackAvg = packAvg;
		}
		var t = temperature;
		temperature *= COOLING_RATE;
//		console.log(temperature +"=" +t+"*"+COOLING_RATE);
		percentage = 100 - ((temperature - ABSOLUTE_ZERO) / (initTemp - ABSOLUTE_ZERO) * 100);

	}
	else {
		percentage = 100;
		console.log("DONE:" + best_cost);
		paint();
		clearInterval(solverInt);
		clearInterval(painterInt);
	}
}

function paint() {
	
	var i = bestPsums.length;
	var col1txt = "Capacity";
	var col2txt = "Divergence";
	var col3txt = "Deviation";
	tsp_ctx.font = '12px sans-serif';
	var col1 = tsp_ctx.measureText(col1txt).width;
	var col2 = tsp_ctx.measureText(col2txt).width;
	var col3 = tsp_ctx.measureText(col3txt).width;
	tsp_ctx.font = '24px sans-serif';
	while(i--) {
		if(bestPsums[i] && tsp_ctx.measureText(bestPsums[i].toString()).width > col1) {
			col1 = tsp_ctx.measureText(bestPsums[i]).width;
		}

		if(bestPDivergence[i] && tsp_ctx.measureText(bestPDivergence[i].toString()).width > col2) {
			col2 = tsp_ctx.measureText(bestPDivergence[i]).width;
		}

		if(bestPDeviation[i] && tsp_ctx.measureText(bestPDeviation[i].toString()).width > col3) {
			col3 = tsp_ctx.measureText(bestPDeviation[i]).width;
		}
	}
	$("#tsp-canvas").attr("width",40+(packP * (35 + 10) + col1 + col2 + col3));
	$("#tsp-canvas").attr("height",90+ packS * (60 + 10 + 10) + (hasExtra ? 100:0));
	tsp_ctx.clearRect(0,0, tsp_canvas.width, tsp_canvas.height);
	tsp_ctx.font = '24px sans-serif';
	var text = "Current best solution";
	tsp_ctx.fillText(text, 10, 20);
	
	tsp_ctx.font = '24px sans-serif';
	tsp_ctx.fillText("Avg Parallel:"+Math.round(bestPackAvg * 100) / 100, 10, 45 + packS * (60 + 10 + 10));

	tsp_ctx.fillText("Current error:"+Math.round(best_cost * 100) / 100, 10, 70 + packS * (60 + 10 + 10));
	if(hasExtra)
		tsp_ctx.fillText("Extra Batteries", 10, 95 + packS * (60 + 10 + 10));
	//console.log("paint");

	tsp_ctx.font = '12px sans-serif';
	tsp_ctx.fillText(col1txt,  0 + 10 + packP * (35 + 10), 50);
	tsp_ctx.fillText(col2txt, 10 + 10 + packP * (35 + 10) + col1, 50);
	tsp_ctx.fillText(col3txt, 20 + 10 + packP * (35 + 10) + col1 + col2, 50);
	tsp_ctx.font = '24px sans-serif';
	i = bestPsums.length;
	while(i--)
	{
		tsp_ctx.fillText(bestPsums[i],   0 + 10 + packP * (35 + 10), i * (60 + 10 + 10));
		tsp_ctx.fillText(Math.round(bestPDivergence[i] * 100) / 100, 10 + 10 + packP * (35 + 10) + col1, i * (60 + 10 + 10));
		tsp_ctx.fillText(Math.round(bestPDeviation[i] * 100) / 100,  20 + 10 + packP * (35 + 10) + col1 + col2, i * (60 + 10 + 10));
	}
	var ep = 1;
	for (var key in bestPack) {
		//console.log(key + ":" + bestPack[key].s + "," + bestPack[key].p);
		if(bestPack[key].extra == false) {
			drawBatt(bestPack[key].s,bestPack[key].p,bestPack[key].bat.id, bestPack[key].bat.value);
		}
		else {
			drawBatt(packS + 2, ep,bestPack[key].bat.id, bestPack[key].bat.value);
			ep = ep + 1;
		}
	}
	$("#progress").css("width", percentage + "%");
	if(percentage == 100) {
		$("#info").text("Done");
		$("#info").show();
	}
	console.log(percentage);


}
function drawBatt(s, p, id, val) {
	var yStep = 60 + 10 + 10;
	var xStep = 35 + 10;
	var x = (p - 1) * xStep;
	var y = (s - 1) * yStep;
	tsp_ctx.font = '14px serif';
	tsp_ctx.fillText('#'+id, 10 + x, 60 + y);
  	tsp_ctx.fillText(val, 10 + x, 80 + y);
  	tsp_ctx.strokeRect(7 + x,40 + y, 35, 60);
  	tsp_ctx.strokeRect(17 +x,30 + y, 15, 10);
}

function clone(obj) {
    var copy;

    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}