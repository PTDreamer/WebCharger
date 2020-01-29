$(document).ready(function() {
	$("#menu").load("menu.php", function() {
if(window.location.pathname === "/cheali/packer.html") {
		$("#packer").addClass("active");
	} 
else if(window.location.pathname === "/cheali/batlist.php") {
		$("#batlist").addClass("active");
	}
else if(window.location.pathname === "/cheali/editbat.php") {
        $("#batlist").addClass("active");
    }
var intervalID = setInterval(checkChargers, 5000);
});
});
function checkChargers() {
    $.getJSON("database.php?special=chargers" , function( data ) {
        if(data.error == "none") {
            $.each( data.result, function( idx, obj ) {
                if(($("a[serverID='" + obj.id + "']").length == 0) && (timeDifference(obj.lastseen) < 5)) {  
                $("#chargers").append($('<a>', { 
                    text : obj.name,
                    attr : {'class':"dropdown-item", 'serverid':obj.id, 'href':'http://'+obj.ip, "lastseen":obj.lastseen}
                }));
                }
                if(($("a[serverID='" + obj.id + "']").length) && (timeDifference(obj.lastseen) > 5)) {  
                    $("a[serverID='" + obj.id + "']").remove();
                }
            });
        }
    });
}

function showMessage(type, text) {
    var boldText;
    var classTxt;
    if(type == "warning") {
        boldText = "Warning";
        classTxt = "alert-warning";
    } else if(type == "error") {
        boldText = "Error";
        classTxt = "alert-danger";
    } else if(type == "info") {
        boldText = "Information";
        classTxt = "alert-info"
    } else if(type == "success") {
        boldText = "Success"
        classTxt = "alert-success";
    }
    var html = $('<div class="alert ' + classTxt + '"><strong>' + boldText + '</strong>' + '  ' + text + '</div>');
    $(".maincontainer").prepend(html);
    $('html, body').animate({scrollTop: '0px'}, 500);
    html.delay(5000).fadeOut(500);
    setTimeout(function() {
        html.remove();
    }, 10000);
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
function timeDifference(date2) {
        let dateTimeParts= date2.split(/[- :]/); 
        dateTimeParts[1]--;

        const date = new Date(...dateTimeParts);
        var date1 = new Date();
        var difference = date1.getTime() - date.getTime();
        return difference / 1000 / 60;
}