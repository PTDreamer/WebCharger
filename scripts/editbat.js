var COND_NiXX = 1;
var COND_Pb = 2;
var COND_LiXX = 4;
var COND_NiZn = 8;
var COND_Unknown = 16;
var COND_LED = 32;

var COND_enableT = 256;
var COND_enable_dV = 512;
var COND_enable_dT = 1024;
var COND_advanced = 32768;

var COND_LiXX_NiZn = (COND_LiXX + COND_NiZn);
var COND_LiXX_NiZn_Pb = (COND_LiXX + COND_NiZn + COND_Pb);
var COND_LiXX_NiZn_Pb_Unkn = (COND_LiXX + COND_NiZn + COND_Pb + COND_Unknown);
var COND_NiXX_Pb = (COND_NiXX + COND_Pb);

var COND_BATTERY = (COND_NiXX + COND_Pb + COND_LiXX + COND_NiZn + COND_Unknown);
var COND_BATT_UNKN = (COND_NiXX + COND_Pb + COND_LiXX + COND_NiZn);


var COND_enableT = 256;
var COND_enable_dV = 512;
var COND_enable_dT = 1024;
var COND_advanced = 32768;

var EDIT_MENU_MANDATORY = 0x8000

var VNominal = 0;
var VCharged = 1;
var VDischarged = 2;
var VStorage = 3;
var VvalidEmpty = 4;
var LAST_VOLTAGE_TYPE = 5;

var NoneBatteryType = 0;
var NiCd = 1;
var NiMH = 2;
var Pb = 3;
var Life = 4;
var Lilo = 5;
var Lipo = 6;
var Li430 = 7;
var Li435 = 8;
var NiZn = 9;
var UnknownBatteryType = 10;
var LED = 11;
var LAST_BATTERY_TYPE = 12;

var ClassNiXX = 0;
var ClassPb = 1;
var ClassLiXX = 2;
var ClassNiZn = 3;
var ClassUnknown = 4;
var ClassLED = 5;
var LAST_BATTERY_CLASS = 6;

var ANALOG_MAX_TIME_LIMIT = 1000;
var ANALOG_MIN_CHARGE = 0.1 * 1000;
var ANALOG_MAX_CHARGE = 65000;

var settings = {
	maxIc : ANALOG_AMP(5.000),
	maxId : ANALOG_AMP(1.000),
	minIc : ANALOG_AMP(0.050),
	minId : ANALOG_AMP(0.050),
	maxPc : ANALOG_WATT(50.000),
	maxPd : ANALOG_WATT(5.000)
}
var battery = {
	type : 0,
	Vc_per_cell : 0,
	Vd_per_cell : 0,
	capacity : 0,
	cells : 0,
	time : 0,
	enable_externT : 0,
	externTCO : 0,
	enable_adaptiveDischarge : 0,
	DCRestTime : 0,
	capCutoff : 0,
	delta_v_enable : true,
	delta_v : 0,
	delta_v_ignore_t : 0,
	delta_t : 0,
	dc_cycles : 0,
	balancer_error : 0,
	Vs_per_cell : 0,
	cells : 0
}
var conditions =
{"Vc_per_cell_forLixx" : (COND_advanced + COND_LiXX_NiZn_Pb),
"Vc_per_cell_forUnk" : (COND_Unknown),
"Vc_per_cell_forNixx" : (COND_advanced + COND_NiXX),
"Vc_per_cell_forLED" : (COND_LED),
"Vs_per_cell" : (COND_advanced + COND_LiXX),
"Vd_per_cell_forUnk" : (COND_BATT_UNKN + COND_advanced),
"Vd_per_cell" : (COND_Unknown),
"capacity" : (COND_BATTERY),
"Ic" : (COND_BATTERY + COND_LED),
"minIc" : (COND_advanced + COND_LiXX_NiZn_Pb_Unkn),
"Id" : (COND_BATTERY),
"minId" : (COND_advanced + COND_BATTERY),
"balancer_error" : (COND_advanced + COND_LiXX_NiZn),
"delta_v_enable" : (COND_NiXX),
"delta_v" : (COND_enable_dV),
"delta_v_ignore_t" : (COND_enable_dV),
"enable_externT" : (COND_BATTERY),
"delta_t" : (COND_enable_dT),
"externTCO" : (COND_enableT),
"DCRestTime" : (COND_advanced + COND_BATTERY),
"enable_adaptiveDischarge" : (COND_advanced + COND_BATTERY),
"dc_cycles" : (COND_advanced + COND_BATTERY),
"capCutoff" : (COND_BATTERY),
"time" : (COND_BATTERY)};

var advanced = false;

var batteryClassMap = [
/*None*/    ClassUnknown,
/*NiCd*/    ClassNiXX,
/*NiMH*/    ClassNiXX,
/*Pb*/      ClassPb,
/*Life*/    ClassLiXX,
/*Lilo*/    ClassLiXX,
/*Lipo*/    ClassLiXX,
/*Li430*/   ClassLiXX,
/*Li435*/   ClassLiXX,
/*NiZn*/    ClassNiZn,
/*Unknown*/ ClassUnknown,
/*LED*/     ClassLED
];

var voltsPerCell  =
[
//          [ VNominal,           VCharged,           VDischarged,        VStorage,           VvalidEmpty[;
/*None*/    [ 1,                  1,                  1,                  1,                  1],
/*NiCd*/    [ ANALOG_VOLT(1.200), ANALOG_VOLT(1.800), ANALOG_VOLT(0.850), 0,                  ANALOG_VOLT(0.850)],
//http://en.wikipedia.org/wiki/Nickel%E2%80%93metal_hydride_battery
//http://eu.industrial.panasonic.com/sites/default/pidseu/files/downloads/files/ni-mh-handbook-2014_interactive.pdf
//http://www6.zetatalk.com/docs/Batteries/Chemistry/Duracell_Ni-MH_Rechargeable_Batteries_2007.pdf
/*NiMH*/    [ ANALOG_VOLT(1.200), ANALOG_VOLT(1.800), ANALOG_VOLT(1.000), 0,                  ANALOG_VOLT(1.000)],

//Pb based on:
//http://www.battery-usa.com/Catalog/NPAppManual%28Rev0500%29.pdf
//charge start current 0.25C (stage 1 - constant current)
//charge end current 0.05C (end current = start current / 5) (stage 2 - constant voltage)
//Stage 3 (float charge) - not implemented
//http://batteryuniversity.com/learn/article/charging_the_lead_acid_battery
/*Pb*/      [ ANALOG_VOLT(2.000), ANALOG_VOLT(2.450), ANALOG_VOLT(1.750), ANALOG_VOLT(0.000), ANALOG_VOLT(1.900)],

//LiXX
//based on imaxB6 manual
//https://github.com/stawel/cheali-charger/issues/184
/*Life*/    [ ANALOG_VOLT(3.300), ANALOG_VOLT(3.600), ANALOG_VOLT(2.500), ANALOG_VOLT(3.300), ANALOG_VOLT(3.000)],
//based on imaxB6 manual
/*Lilo*/    [ ANALOG_VOLT(3.600), ANALOG_VOLT(4.100), ANALOG_VOLT(2.500), ANALOG_VOLT(3.750), ANALOG_VOLT(3.500)],
/*LiPo*/    [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.200), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],
/*Li430*/   [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.300), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],
/*Li435*/   [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.350), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],

//based on "mars" settings, TODO: find datasheet
/*NiZn*/    [ ANALOG_VOLT(1.600), ANALOG_VOLT(1.900), ANALOG_VOLT(1.300), ANALOG_VOLT(1.600), ANALOG_VOLT(1.400)],

/*Unknown*/ [ 1,                  ANALOG_VOLT(4.000), ANALOG_VOLT(2.000), 1,                  1],
//PowerSupply
/*LED*/     [ 1,                  ANALOG_VOLT(4.000), 1,                  1,                  1],

];
function getDefaultVoltagePerCell(x) {
	return voltsPerCell[battery.type][x];
}
function ANALOG_CHARGE(x) {
	return x * 1000;
}
function ANALOG_VOLT(x) {
	return x * 1000;
}
function ANALOG_AMP(x) {
	return x * 1000;
}
function ANALOG_WATT(x) {
	return x * 100;
}

function ANALOG_CELCIUS(x) {
	return x * 100;
}
function loadBattery() {
	battery.type = $("#type").val();
	if($("#Vc_per_cell_forLixx").prop( "disabled") == false) {
		battery.Vc_per_cell = $("#Vc_per_cell_forLixx").val();
	}
	if($("#Vc_per_cell_forUnk").prop( "disabled") == false) {
		battery.Vc_per_cell = $("#Vc_per_cell_forUnk").val();
	}
	if($("#Vc_per_cell_forNixx").prop( "disabled") == false) {
		battery.Vc_per_cell = $("#Vc_per_cell_forNixx").val();
	}
	if($("#Vc_per_cell_forLED").prop( "disabled") == false) {
		battery.Vc_per_cell = $("#Vc_per_cell_forLED").val();
	}
	if($("#Vd_per_cell_forUnk").prop( "disabled") == false) {
		battery.Vd_per_cell = $("#Vd_per_cell_forUnk").val();
	}
	if($("#Vd_per_cell").prop( "disabled") == false) {
		battery.Vd_per_cell = $("#Vd_per_cell").val();
	}
	battery.capacity = $("#capacity").val();
	battery.cells = $("#cells").val();
	battery.time = $("#time").val();
	battery.enable_externT = $("#enable_externT").prop("checked");
	battery.externTCO = $("#externTCO").val() * 100;
	battery.enable_adaptiveDischarge = $("#enable_adaptiveDischarge").prop("checked");
	battery.DCRestTime = $("#DCRestTime").val();
	battery.capCutoff = $("#capCutoff").val();
	battery.delta_v_enable = $("#delta_v_enable").prop("checked");
	battery.delta_v = $("#delta_v").val();
	battery.delta_v_ignore_t = $("#delta_v_ignore_t").val();
	battery.delta_t = $("#delta_t").val() * 100;
	battery.dc_cycles = $("#dc_cycles").val();
	battery.balancer_error = $("#balancer_error").val();
	battery.Vs_per_cell = $("#Vs_per_cell").val();
	battery.Ic = $("#Ic").val();
	battery.Id = $("#Id").val();
	battery.minIc = $("#minIc").val();
	battery.minId = $("#minId").val();

	battery.subtype = $("#subtype").val();
	battery.charge_state = $("#charge_state").val();
	battery.use_state = $("#use_state").val();
	battery.last_capacity = $("#last_capacity").val();
	battery.part_of_pack_id = $("#part_of_pack_id").val();
	battery.isPack = $("#isPack").prop("checked");

}
//$idx, $name, -$type, -$capacity, -$cells, -$Ic, -$Id, -$Vc_per_cell, -$Vd_per_cell, $minIc, $minId, -$time, -$enable_externT
//-$externTCO, -$enable_adaptiveDischarge, -$DCRestTime, -$capCutoff, -$Vs_per_cell, 
//-$balancer_error, -$delta_v_enable, -$delta_v, -$delta_v_ignore_t, -$delta_t, -$dc_cycles, $date_created
function saveBattery() {
	$("#type").val(battery.type);
	$("#Vc_per_cell_forLixx").val(Math.floor(battery.Vc_per_cell));
	$("#Vc_per_cell_forUnk").val(Math.floor(battery.Vc_per_cell));
	$("#Vc_per_cell_forNixx").val(Math.floor(battery.Vc_per_cell));
	$("#Vc_per_cell_forLED").val(Math.floor(battery.Vc_per_cell));
	$("#Vd_per_cell").val(Math.floor(battery.Vd_per_cell));
	$("#Vd_per_cell_forUnk").val(Math.floor(battery.Vd_per_cell));
	$("#capacity").val(Math.floor(battery.capacity));
	$("#cells").val(battery.cells);
	$("#time").val(Math.floor(battery.time));
	$("#enable_externT").prop("checked", battery.enable_externT);
	$("#externTCO").val(Math.floor(battery.externTCO / 100));
	$("#enable_adaptiveDischarge").prop("checked", battery.enable_adaptiveDischarge);
	$("#DCRestTime").val(Math.floor(battery.DCRestTime));
	$("#capCutoff").val(Math.floor(battery.capCutoff));
	$("#delta_v_enable").prop("checked", battery.delta_v_enable);
	$("#delta_v").val(Math.floor(battery.delta_v));
	$("#delta_v_ignore_t").val(Math.floor(battery.delta_v_ignore_t));
	$("#delta_t").val(Math.floor(battery.delta_t / 100));
	$("#dc_cycles").val(Math.floor(battery.dc_cycles));
	$("#balancer_error").val(Math.floor(battery.balancer_error));
	$("#Vs_per_cell").val(Math.floor(battery.Vs_per_cell));
	$("#Ic").val(Math.floor(battery.Ic));
	$("#Id").val(Math.floor(battery.Id));
	$("#minIc").val(Math.floor(battery.minIc));
	$("#minId").val(Math.floor(battery.minId));

	$("#subtype").val(battery.subtype);
	$("#charge_state").val(battery.charge_state);
	$("#use_state").val(battery.use_state);
	$("#last_capacity").val(battery.last_capacity);
	$("#part_of_pack_id").val(battery.part_of_pack_id);
	$("#isPack").prop("checked", battery.isPack);
}
function getBatteryClass() { return batteryClassMap[battery.type];};
function isPowerSupply() { return getBatteryClass() == ClassLED; };
function isLiXX() { return getBatteryClass() == ClassLiXX; };
function isNiXX() { return getBatteryClass() == ClassNiXX; };
function isPb() { return getBatteryClass() == ClassPb; };

function changedType() {

		 	battery.Vc_per_cell = getDefaultVoltagePerCell(VCharged);
		    battery.Vd_per_cell = getDefaultVoltagePerCell(VDischarged);

		    if(battery.type == NoneBatteryType) {
		        battery.capacity = ANALOG_CHARGE(2.000);
		        battery.cells = 3;

		        battery.time = ANALOG_MAX_TIME_LIMIT;
		        battery.enable_externT = false;
		        battery.externTCO = ANALOG_CELCIUS(60);

		        battery.enable_adaptiveDischarge = false;
		        battery.DCRestTime = 30;
		        battery.capCutoff = 120;
		    }

		    if(isNiXX()) {
		        battery.delta_v_enable = true;
		        if(battery.type == NiMH) {
		            battery.delta_v = -ANALOG_VOLT(0.005);
		        } else {
		            battery.delta_v = -ANALOG_VOLT(0.015);
		        }
		        battery.delta_v_ignore_t = 3;
		        battery.delta_t = ANALOG_CELCIUS(1);
		        battery.dc_cycles = 5;
		    } else {
		        battery.balancer_error = ANALOG_VOLT(0.008);
		        battery.Vs_per_cell = getDefaultVoltagePerCell(VStorage);
		    }
		    changedCapacity();
}
function changedIc()
{
    check();
    battery.minIc = battery.Ic/10;
}

function changedId()
{
    check();
    battery.minId = battery.Id/10;
}

function changedCapacity()
{
    check();
    battery.Ic = battery.capacity;
    if(battery.type == Pb)
        battery.Ic/=4;

    changedIc();
    battery.Id = battery.capacity;
    changedId();
}

function check()
{
    var v;

    v = ANALOG_MIN_CHARGE;
    if(battery.capacity < v) battery.capacity = v;
    v = ANALOG_MAX_CHARGE;
    if(battery.capacity > v) battery.capacity = v;

    v = ANALOG_MAX_TIME_LIMIT;

    v = getMaxCells();
    if(battery.cells > v) battery.cells = v;
    v = 1;
    if(battery.cells < v) battery.cells = v;


    v = getMaxIc();
    if(battery.Ic > v) battery.Ic = v;
    v = battery.Ic;
    if(battery.minIc > v) battery.minIc = v;

    v = getMaxId();
    if(battery.Id > v) battery.Id = v;
    v = battery.Id;
    if(battery.minId > v) battery.minId = v;

    v = settings.minIc;
    if(battery.Ic < v) battery.Ic = v;
    if(battery.minIc < v) battery.minIc = v;

    v = settings.minId;
    if(battery.Id < v) battery.Id = v;
    if(battery.minId < v) battery.minId = v;
}

function getMaxIc()
{
    var v = getDefaultVoltage(VDischarged);
    var i = evalI(settings.maxPc, v);
    if(i > settings.maxIc)
        i = settings.maxIc;
    return i;
}

function getMaxId()
{
    var v = getDefaultVoltage(VDischarged);
    var i = evalI(settings.maxPd, v);

    if(i > settings.maxId)
        i = settings.maxId;
    return i;
}

function getMaxCells()
{
    if(battery.type == UnknownBatteryType || battery.type == LED)
        return 1;
    var v = getDefaultVoltagePerCell(VCharged);
    return 27000 / v;
}

function evalI(P, U) {
    var i = P;
    i *= ANALOG_VOLT(1);
    i /= U;
    i *= ANALOG_AMP(1);
    i /= ANALOG_WATT(1);
    return i;
}

function getSelector() {
    var result = 1<<14;
    if(battery.type != NoneBatteryType) {
        result += 1 << getBatteryClass();

        if(battery.enable_externT) {
            result += COND_enableT;
            if(isNiXX()) {
                result += COND_enable_dT;
            }
        }
        if(isNiXX() && battery.delta_v_enable) {
            result += COND_enable_dV;
        }
        if(advanced) {
            result += COND_advanced;
        }
    }
    return result;
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;
    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }
    return params;
}

$(document).ready(function() {
	Object.keys(conditions).forEach(function(key) {
		var notFound = true;
		$( ".form-group" ).each(function( index ) {
			if($(this).children("input").attr("id") == key)  {
		    	notFound = false;
				$(this).attr("condition", conditions[key]);
			}
		});
		if(notFound) {
			console.log("BIG ERROR, NOT FOUND:" + key);
		}
	});
	var $_GET = getQueryParams(document.location.search);

	loadImage("images/"+$_GET["id"]+".jpeg");
    $("#subtypeIndex").append($('<option>', { 
    	value: -1,
    	text : 'none',
    	attr : {'data-type':-1}
    }));

	$.getJSON( `database.php?special=types`, function( data ) {
		if(data.error == "none") {
  		$.each( data.battery_types, function( idx, obj ) {
    	    $("#type").append($('<option>', { 
       		value: obj.type,
       		text : obj.name
    		}));
    	});
    	$.each( data.battery_sub_types, function( idx, obj ) {
    	    $("#subtypeIndex").append($('<option>', { 
       		value: obj.subtype,
       		text : obj.name,
       		attr : {'data-type':obj.type}
    		}));
    	});
    	$.each( data.use_states, function( idx, obj ) {
    	    $("#use_state").append($('<option>', { 
       		value: obj.use_state,
       		text : obj.name
    		}));
    	});
    	$.each( data.charge_state_types, function( idx, obj ) {
    	    $("#charge_state").append($('<option>', { 
       		value: obj.charge_state,
       		text : obj.name
    		}));
    	});
  		} else {
  			showMessage("error", data.error);
  		}

  		$.getJSON( `database.php?id=${$_GET["id"]}`, function( data ) {
  			if(data.error == "none") {
	  		$.each( data.result, function( key, val ) {
	    		if($("input:checkbox[name='"+key+"']").length) {
	    			console.log("CHECKBOX:"+key+","+val)
	    			$("input:checkbox[name='"+key+"']").prop("checked", val);
	    		}
	    		else if($("[name='"+key+"']").length) {
					$("[name='"+key+"']").val(val);
					console.log("FOUND " + key + val);
				}
				else {
					console.log("NOT FOUND" + key);
				}
	    		
	    	});
	   		} else {
  				showMessage("error", data.error);
  			}
	  		loadBattery();
			checkVisible();
  		});

  	});
});

function predicate(condition, selector){
	var display = true;
	if(condition & EDIT_MENU_MANDATORY) {
		display = selector & EDIT_MENU_MANDATORY;
		condition ^= EDIT_MENU_MANDATORY;
	}
	return display && (condition & selector);
}

function checkVisible() {
	loadBattery();
	check();
	var v = getSelector();
	console.log("selector " + v)
	$( ".form-group" ).each(function( index ) {
		if(!isNaN($(this).attr("condition"))) {
			if(predicate($(this).attr("condition"), v) == false) {
				$(this).hide();
				$(this).children("input").prop( "disabled", true );
			}
			else {
				$(this).show();
				$(this).children("input").prop( "disabled", false);
			}
		}
	});

}

function getDefaultVoltage(type)
{
    var cells = battery.cells;
    var voltage = getDefaultVoltagePerCell(type);

    if(type == VDischarged && battery.type == NiMH && cells > 6) {
        //based on http://eu.industrial.panasonic.com/sites/default/pidseu/files/downloads/files/ni-mh-handbook-2014_interactive.pdf
        //page 11: "Discharge end voltage"
        cells--;
        voltage = ANALOG_VOLT(1.200);
    }
    return cells * voltage;
}

function setAdvanced(x) {
	advanced = x;
}

function compress(e) {
            const width = 400;
            const height = 300;
            const fileName = e.target.files[0].name;
            const reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]);
            reader.onload = event => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    var preview = document.querySelector('#bat_image');
                    const elem = document.createElement('canvas');
                    const scaleFactor = width / img.width;
                    elem.width = width;
                    elem.height = img.height * scaleFactor;
                    const ctx = elem.getContext('2d');
                    // img.width and img.height will contain the original dimensions
                    ctx.drawImage(img, 0, 0, width, img.height * scaleFactor);
                    ctx.canvas.toBlob((blob) => {
                    	myBlob = blob;
                        preview.src = window.URL.createObjectURL(blob);
                        console.log(preview.src);
                    }, 'image/jpeg', 1);
                },
                        reader.onerror = error => console.log(error);
            };
        }
var myBlob;
function afterLoad() {
	$("form button[type=submit]").click(function() {
    	$("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
		$(this).attr("clicked", "true");
	});
var form = document.forms.namedItem("mainForm");
form.addEventListener('submit', function(ev) {
	var val = $("button[type=submit][clicked=true]").attr("name");
	console.log(val);
	var advanced_back = advanced;
	advanced = true;
	checkVisible();
	var oData = new FormData(form);
	oData.delete("upfile");
	if(val == "new") {
		oData.append("new", true);
	}
	 if(myBlob) {
	  	oData.append("myfile", myBlob, "filename.txt");
	}
	if($("#part_of_pack_id").val() =="")
		oData.delete("part_of_pack_id");
	if($("#subtype").val() ==-1)
		oData.delete("subtype");
	var oReq = new XMLHttpRequest();
	oReq.responseType = 'json';
	var $_GET = getQueryParams(document.location.search);
	oReq.open("POST", "database.php?id="+$_GET["id"], true);
	oReq.onload = function(oEvent) {
	  	console.log(oReq.response);
	    if (oReq.status == 200) {
	    	if(oReq.response["error"] == "none") {
	    		if(val == "new") {
	    			window.location.href = location.protocol + '//' + location.host + location.pathname
 + '?id=' + oReq.response["result"].idx;
	    			return;
	    		} else {
	      			showMessage("success","Record saved");
	      		}
	    	}
	      	else
	      		showMessage("error","ERROR Record NOT saved:"+ oReq.response["error"]);
	    } else {
	      		showMessage("error","ERROR Record NOT saved:");
	    }
	advanced = advanced_back;
	checkVisible();
  };

  oReq.send(oData);
  ev.preventDefault();
}, false);
}
function loadImage(image_url){

    var http = new XMLHttpRequest();

    http.open('HEAD', image_url, false);
    http.send();

    if(http.status != 404) {
    	$("#bat_image").attr("src",image_url);
    	console.log("IMAGE FOUND");
    }
    else {
    	$("#bat_image").attr("src","images/no-image-available.jpg");
    }

}

function openInNewTab(url) {
  var win = window.open(url, '_blank');
  win.focus();
}