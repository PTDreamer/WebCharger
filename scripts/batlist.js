var currentPage = 1;
var totalPages;
var bc = new BroadcastChannel('qrcodeReader');
var batTypes = Array();
var batSubTypes = Array();
bc.onmessage = function (ev) { console.log(ev); }

$(document).ready(function() {
  $("#typefilter").append($('<option>') 
    .attr('value',-1)
    .text("ANY")
    );
  $("#subtypefilter").append($('<option>') 
    .attr('value',-1)
    .text("ANY")
    );
  $( "#qrbtn" ).click(function() {
    var win = window.open('qrcode_read.html', '_blank');
    win.focus();
  });
  $("#addnew").click(function() {
      addNewBattery();
    });
  $("#createpack").click(function() {
    var url = `packer.html?page=all`;
    if($('#batnamefind').val()) {
      name = $('#batnamefind').val();
      url += `&name=${name}`
    }
    if($('#idd').val()) {
      name = $('#idd').val();
      url += `&idd=${name}`
    }
    if($('#typefilter').val() != -1) {
      name = $('#typefilter').val();
      url += `&type=${name}`
    }
    if($('#subtypefilter').val() != -1) {
      name = $('#subtypefilter').val();
      url += `&subtype=${name}`
    }
    var win = window.open(url, '_blank');
    win.focus();
  });
  $.getJSON( `database.php?special=batTypes`, function( data ) {
    if(data.error == "none") {
      $.each( data.result, function( idx, obj ) {
        batTypes[obj.type] = obj.name;
        $("#typefilter").append($('<option>') 
          .attr('value',obj.type)
          .text(obj.name)
          );
      }) 
      $.getJSON( `database.php?special=batSubTypes`, function( data ) {
        if(data.error == "none") {
          $.each( data.result, function( idx, obj ) {
            batSubTypes[obj.subtype] = obj.name;
            $("#subtypefilter").append($('<option>') 
              .attr('value',obj.subtype)
              .text(obj.name)
              );
          });
          loadPage(1);
          checkVisible(); 
        };
      });
    };
  });
  $("body").on("click", ".delete", function() {
    event.preventDefault();
    if (confirm("Are you sure you want to delete this Battery?")) {
      $.getJSON( $(this).attr('href'), function( data ) {
        if(data.error = 'none') {
          showMessage('success', 'Battery deleted');
          loadPage(currentPage);
        }
        else {
          showMessage('error', 'There was a problem deleting the battery:'+ data.error);
        }
    });
    }
  });
  $('#batnamefind').bind('input propertychange', function() {
    loadPage(currentPage);
  });
  $('#idd').bind('input propertychange', function() {
    loadPage(currentPage);
  });
  $('#subtypefilter').bind('input propertychange', function() {
    loadPage(currentPage);
  });
  $('#typefilter').bind('input propertychange', function() {
    loadPage(currentPage);
  });
  $('#isPack').change(function() {
    loadPage(currentPage);
  });
  $('#partOfPack').change(function() {
    loadPage(currentPage);
  });
  $('#nextBtn').click(function (event) 
  {
   event.preventDefault();
   ++currentPage;
   loadPage(currentPage);
   checkVisible();
 });
  $('#previousBtn').click(function (event)
  {
   event.preventDefault();
   --currentPage;
   loadPage(currentPage);
   checkVisible();
 });

  var $_GET = getQueryParams(document.location.search);
  if($_GET["partofpack"]) {
    $("#idd").val($_GET["partofpack"]);
  }
  if($_GET["pack"]) {
    $("#partOfPack").val($_GET["pack"]);
  }
})
function checkVisible() {
  if(currentPage == totalPages) {
    $('#nextBtn').hide();   
  }
  else {
    $('#nextBtn').show();   
  }
  if(currentPage == 1) {
    $('#previousBtn').hide();   
  }
  else {
    $('#previousBtn').show();   
  }
}

function loadPage(page) {
  var url = `database.php?page=${page}`;
  if($('#batnamefind').val()) {
    name = $('#batnamefind').val();
    url += `&name=${name}`
  }
  if($('#idd').val()) {
    name = $('#idd').val();
    url += `&idd=${name}`
  }
  if($('#typefilter').val() != -1) {
    name = $('#typefilter').val();
    url += `&type=${name}`
  }
  if($("#isPack").prop("checked")) {
    url += `&isPack=1`
  }
  if($('#subtypefilter').val() != -1) {
    name = $('#subtypefilter').val();
    url += `&subtype=${name}`
  }
  if($('#partOfPack').val()) {
    name = $('#partOfPack').val();
    url += `&pack=${name}`
  }
  $.getJSON(url , function( data ) {
    if(data.error == "none") {
      totalPages = Math.ceil(data.totalrecords / 5);
      $(".hint-text").html("Showing page <b>"+ page + "</b> out of <b>"+ totalPages +"</b>");
      $("#battable").empty();
      $.each( data.result, function( idx, obj ) {  
        $dbres = $(this)[0];
        $img  = getImageFromID($dbres.idx);
        var use_state;
        if($dbres.use_state == 1)
          use_state = '<span class="status text-info ">&bull;</span>Unknown';
        else if($dbres.use_state == 2)
          use_state = '<span class="status text-info ">&bull;</span>Part of pack';
        else if($dbres.use_state == 3)
          use_state = '<span class="status text-danger ">&bull;</span>To be disposed';
        else if($dbres.use_state == 4)
          use_state = '<span class="status text-danger ">&bull;</span>Disposed';
        else if($dbres.use_state == 5)
          use_state = '<span class="status text-success ">&bull;</span>In use';
        else if($dbres.use_state == 6)
          use_state = '<span class="status text-warning ">&bull;</span>Not in use';
        else
          use_state = '<span class="status text-info ">&bull;</span>Unknown';
        $("#battable").append($('<tr>')
          .append($('<td>') 
            .text($dbres.idx)
            .attr("class", $dbres.isPack ? "pack" : "")
            )
          .append($('<a>')
            .attr('href', "editbat.php?id="+$dbres.idx)
            .append($('<img>')
              .attr('src', $img)
              .attr('class', "avatar")
              .attr('alt', "Avatar")
              .text($dbres.name)
              )
            )
          .append($('<td>') 
            .text($dbres.name)
            )
          .append($('<td>') 
            .text($dbres.cells)
            )
          .append($('<td>') 
            .text($dbres.capacity)
            )
          .append($('<td>') 
            .text(batTypes[$dbres.type])
            )
          .append($('<td>') 
            .text(batSubTypes[$dbres.subtype])
            )
          .append($('<td>') 
            .text($dbres.date_created)
            )
          .append($('<td>') 
            .append(use_state)
            )
          .append($('<td>') 
            .append($('<a>')
              .attr('href', "editbat.php?id=" + $dbres.idx)
              .attr('class', "use_bat")
              .attr('title', "use")
              .attr('data-toggle', "tooltip")
              .attr('title', "use")
              .append('<i class="material-icons">&#xe038;</i>')             
              )
            .append($('<a>')
              .attr('href', "editbat.php?id=" + $dbres.idx)
              .attr('class', "settings")
              .attr('title', "edit")
              .attr('data-toggle', "tooltip")
              .append('<i class="material-icons">&#xE8B8;</i>')             
              )
            .append($('<a>')
              .attr('href', "database.php?delete=" + $dbres.idx)
              .attr('class', "delete")
              .attr('data-toggle', "tooltip")
              .attr('title', "Delete")
              .append('<i class="material-icons">&#xE5C9;</i>')             
              )
            .append($('<a>')
              .attr('href', "batlist.php?pack=" + $dbres.idx)
              .attr('class', "batteryPack")
              .attr('class', $dbres.isPack == 0 ? "d-none" : "")
              .attr('data-toggle', "tooltip")
              .attr('title', "Filter pack batteries")
              .append('<i class="material-icons" style="color:green">battery_full</i>')             
              )
            .append($('<a>')
            .attr('href', "batlist.php?partofpack=" + $dbres.part_of_pack_id)
            .attr('class', "partOfPack")
            .attr('class', $dbres.part_of_pack_id == null ? "d-none" : "")
            .attr('data-toggle', "tooltip")
            .attr('title', "Filter battery pack")
            .append('<i class="material-icons" style="color:green">battery_alert</i>')             
             )
            )
          );
      });
    }  
    else 
    {
   // showMessage("error", data.error);
   console.log("ERROR" + data.error);
 }
});
}

$(function () {
  $('.button-checkbox').each(function () {

        // Settings
        var $widget = $(this),
        $button = $widget.find('button'),
        $checkbox = $widget.find('input:checkbox'),
        color = $button.data('color'),
        settings = {
          on: {
            icon: 'fa fa-check-square-o'
          },
          off: {
            icon: 'fa fa-square-o'
          }
        };

        // Event Handlers
        $button.on('click', function () {
          $checkbox.prop('checked', !$checkbox.is(':checked'));
          $checkbox.triggerHandler('change');
          updateDisplay();
        });
        $checkbox.on('change', function () {
          updateDisplay();
        });

        // Actions
        function updateDisplay() {
          var isChecked = $checkbox.is(':checked');

            // Set the button's state
            $button.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $button.find('.state-icon')
            .removeClass()
            .addClass('state-icon ' + settings[$button.data('state')].icon);

            // Update the button's color
            if (isChecked) {
              $button
              .removeClass('btn-primary')
              .addClass('btn-' + color + ' active');
            }
            else {
              $button
              .removeClass('btn-' + color + ' active')
              .addClass('btn-primary');
            }
          }

        // Initialization
        function init() {

          updateDisplay();

            // Inject the icon if applicable
            if ($button.find('.state-icon').length == 0) {
              $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
            }
          }
          init();
        });
});

function getImageFromID(id) {
  var http = new XMLHttpRequest();
  url = "images/"+id+".jpeg";
  http.open('HEAD', url, false);
  http.send();
  if(http.status != 404)
    return url;
  return "images/no-image-available.jpg"
}

function addNewBattery() {
$.getJSON("database.php?createNew" , function( data ) {
    if(data.error == "none") {
      window.location.href = 'editbat.php?id='+data.newID;
    }
});
}