function openSubScreen(scrn) {
  $(".modal").hide();
  $("#" + scrn).show();
}

function openAddMenu(scrn) {
  // $(".modalAdd").show();
  $("#" + scrn).show();
}

function randomizePos(e) {
  var offsetX = Math.random() * 0.005 - 0.0025;
  var offsetY = Math.random() * 0.005 - 0.0025;
  e.latitude = e.latitude + offsetY;
  e.longitude = e.longitude + offsetX;
  e.latlng = L.latLng([e.latitude, e.longitude]);
  return e;
}

//function randomizePos(e){
//    return e;
//}

function startAutolocate() {
  $("#btnAutolocate").html("On");
  storeSettings();
  clearInterval(intAutolocate);
  intAutolocate = setInterval(function () {
    if (mrkCurrentLocation) {
      mrkCurrentLocation.remove();
    }
    if ($("#btnFilter").html() == "On") {
      var flt = $("#numFilter").val();
    } else {
      var flt = 100000;
    }
    if (posCurrent.accuracy < flt) {
      var radius = Math.min(200, posCurrent.accuracy / 2);
      radius = Math.max(10, radius);
      mrkCurrentLocation = L.circle(posCurrent.latlng, {
        radius: radius,
      }).addTo(mymap);
      mymap.setView(posCurrent.latlng, 17);
    }
    if ($("#mode").html().substring(0, 10) == "NAVIGATION") {
      if (lyrNavLine) {
        lyrNavLine.remove();
      }
      var jsn = lyrNavTarget.toGeoJSON();
      if (jsn.type == "Feature") {
        if (jsn.geometry.type == "Point") {
          var ptClosest = lyrNavTarget.getLatLng();
        } else {
          var ptClosest = L.GeometryUtil.closest(
            mymap,
            lyrNavTarget,
            posCurrent.latlng
          );
        }
      } else if ((jsn.type = "FeatureCollection")) {
        var ptClosest = L.GeometryUtil.closest(
          mymap,
          lyrNavTarget.getLayers()[0],
          posCurrent.latlng
        );
      } else {
        alert("Invalid Target");
      }
      if (ptClosest) {
        lyrNavLine = L.polyline([posCurrent.latlng, ptClosest], {
          color: "darkviolet",
          dashArray: "5, 5",
        }).addTo(mymap);
        var dst = posCurrent.latlng.distanceTo(ptClosest);
        var bng = L.GeometryUtil.bearing(posCurrent.latlng, ptClosest);
        $("#navData").html(dst.toFixed(0) + "m @ " + bng.toFixed(0) + "o");
      }
    } else {
      if (lyrNavTarget) {
        lyrNavTarget.remove();
        lyrNavLine.remove();
      }
    }
  }, $("#numAutolocate").val() * 1000);
}

function stopAutolocate() {
  $("#btnAutolocate").html("Off");
  storeSettings();
  clearInterval(intAutolocate);
}

function startBreadcrumbs() {
  $("#btnBreadcrumbs").html("On");
  storeSettings();
  clearInterval(intBreadcrumbs);
  addBreadcrumb();
  intBreadcrumbs = setInterval(function () {
    if ($("#btnFilter").html() == "On") {
      var flt = $("#numFilter").val();
    } else {
      var flt = 100000;
    }
    if (posCurrent.accuracy < flt) {
      addBreadcrumb();
    }
  }, $("#numBreadcrumbs").val() * 1000);
  clearInterval(intInfo);
  intInfo = setInterval(function () {
    populateInfo();
  }, $("#numBreadcrumbs").val() * 1000);
}

function stopBreadcrumbs() {
  $("#btnBreadcrumbs").html("Off");
  storeSettings();
  clearInterval(intBreadcrumbs);
  addBreadcrumb();
}

function addBreadcrumb() {
  if (posCurrent) {
    var radius = Math.min(200, posCurrent.accuracy / 2);
    radius = Math.max(10, radius);
    var mrkBreadcrumb = L.circle(posCurrent.latlng, {
      radius: radius,
      color: "green",
    });
    mrkBreadcrumb.bindPopup(
      "<h4>" +
        L.stamp(mrkBreadcrumb) +
        "</h4>Time: " +
        returnTimeFromUTC(posCurrent.timestamp) +
        "<br>Accuracy: " +
        posCurrent.accuracy +
        " m"
    );
    lyrBreadcrumbs.addLayer(mrkBreadcrumb);
    populatePoints();
  }
}

function startStream() {
  clearInterval(intStream);
  addVertex();
  intStream = setInterval(function () {
    if ($("#btnFilter").html() == "On") {
      var flt = $("#numFilter").val();
    } else {
      var flt = 100000;
    }
    if (posCurrent.accuracy < flt) {
      addVertex();
    }
  }, $("#numStream").val() * 1000);
}

function stopStream() {
  clearInterval(intStream);
  addVertex();
}

function addVertex() {
  if (posCurrent) {
    if ($("#mode").html() != "PAUSE COLLECTION") {
      var radius = Math.min(200, posCurrent.accuracy / 2);
      radius = Math.max(10, radius);
      var mrkVertex = L.circle(posCurrent.latlng, {
        radius: radius,
        color: "red",
      });
      mrkVertex.bindPopup(
        "<h4>" +
          L.stamp(mrkVertex) +
          "</h4>Time: " +
          returnTimeFromUTC(posCurrent.timestamp) +
          "<br>Accuracy: " +
          posCurrent.accuracy +
          " m"
      );
      lyrVertices.addLayer(mrkVertex);
      populatePoints();
    }
  }
}

function populateInfo() {
  if (posCurrent) {
    $(".info_cur_acc").html(posCurrent.accuracy.toFixed(0));
    if (isNaN(posCurrent.altitude)) {
      posCurrent.altitude = "NA";
    } else {
      posCurrent.altitude = posCurrent.altitude.toFixed(1);
    }
    $("#info_cur_lat").val(posCurrent.latitude.toFixed(5));
    $("#info_cur_lng").val(posCurrent.longitude.toFixed(5));
    $("#info_cur_alt").val(posCurrent.altitude);
    $("#info_cur_tm").val(returnTimeFromUTC(posCurrent.timestamp));

    if (posPrevious) {
      $("#info_prv_lat").val(posPrevious.latitude.toFixed(5));
      $("#info_prv_lng").val(posPrevious.longitude.toFixed(5));
      $("#info_prv_alt").val(posPrevious.altitude);
      $("#info_prv_tm").val(returnTimeFromUTC(posPrevious.timestamp));

      var dst = posPrevious.latlng.distanceTo(posCurrent.latlng);
      if (posCurrent.altitude == "NA" || posPrevious.altitude == "NA") {
        var alt = "NA";
      } else {
        var alt = posCurrent.altitude - posPrevious.altitude;
      }
      var tm = (posCurrent.timestamp - posPrevious.timestamp) / 1000;
      var bng = L.GeometryUtil.bearing(posPrevious.latlng, posCurrent.latlng);
      if (alt == "NA") {
        var clr = "NA";
      } else {
        var clr = ((alt / tm) * 60 * 60).toFixed(1);
      }
      $("#info_dif_dst").val(dst.toFixed(1));
      $("#info_dif_alt").val(alt);
      $("#info_dif_tm").val(tm.toFixed(1));
      $("#info_dif_bng").val(bng.toFixed(1));
      $("#info_dif_vel").val((((dst / tm) * 60 * 60) / 1000).toFixed(3));
      $("#info_dif_clr").val(clr);
    }

    posPrevious = posCurrent;
  }
}

function populatePoints() {
  var lyrFocal;
  var lyrPrevious;
  var dst;
  var dstSum = 0;
  var bng;
  var start;
  var strPopup;
  var tm;
  if ($("#mode").html().slice(-10) == "COLLECTION") {
    lyrFocal = lyrVertices;
    $("#hdrPoints").html("Vertex Points");
  } else {
    lyrFocal = lyrBreadcrumbs;
    $("#hdrPoints").html("Breadcrumb Points");
  }
  var strTable =
    "<table class='table'><tr class='table-header'><th>ID</th><th>Time</th><th>Dist (m)</th><th>Bearing</th><th></th></tr>";
  lyrFocal.eachLayer(function (lyr) {
    if (lyrPrevious) {
      strPopup = lyr.getPopup().getContent();
      start = strPopup.indexOf("Time: ") + 6;
      tm = strPopup.substring(start, start + 8);
      dst = lyrPrevious.getLatLng().distanceTo(lyr.getLatLng());
      dstSum += dst;
      bng = L.GeometryUtil.bearing(lyrPrevious.getLatLng(), lyr.getLatLng());
      strTable +=
        "<tr><td>" +
        L.stamp(lyr) +
        "</td><td>" +
        tm +
        "</td><td>" +
        dst.toFixed(1) +
        "</td><td>" +
        bng.toFixed(0) +
        "</td><td><span class='btnFindPt' data-id='" +
        L.stamp(lyr) +
        "'><i class='fa fa-search'</i></span></td></tr>";
      lyrPrevious = lyr;
    } else {
      strPopup = lyr.getPopup().getContent();
      start = strPopup.indexOf("Time: ") + 6;
      tm = strPopup.substring(start, start + 8);
      strTable +=
        "<tr><td>" +
        L.stamp(lyr) +
        "</td><td>" +
        tm +
        "</td><td>NA</td><td>NA</td><td><span class='btnFindPt' data-id='" +
        L.stamp(lyr) +
        "'><i class='fa fa-search'</i></span></td></tr>";
      lyrPrevious = lyr;
    }
  });
  strTable +=
    "<tr class='table-header'><th>Total</th><th></th><th>" +
    dstSum.toFixed(0) +
    "</th><th></th><th></th></tr>";
  strTable += "</table>";
  $("#points").html(strTable);
  $(".btnFindPt").click(function () {
    var id = $(this).attr("data-id");
    var ll = lyrFocal.getLayer(id).getLatLng();
    mymap.setView(ll, 17);
    openSubScreen();
  });
  if ($("#mode").html().slice(-10) != "COLLECTION") {
    $("#points").append(
      "<button id='btnClearCrumbs' class='btn btn-danger btn-block btn-no-top-margin'>Clear Breadcrumbs</button>"
    );
    $("#btnClearCrumbs").click(function () {
      if (confirm("Are you sure you want delete all the crumbs?")) {
        lyrBreadcrumbs.clearLayers();
        if ($("#btnBreadcrumbs").html() == "On") {
          startBreadcrumbs();
        }
        populatePoints();
      }
    });
  }
}

function populateFeatures(tbl) {
  $("#hdrFeatures").html(tbl);
  if (isOnline) {
    $.ajax({
      url: "generic_mobile_resources/php_generic_list.php",
      data: { tbl: tbl, user: user.username },
      type: "POST",
      success: function (response) {
        $("#features").html(response);
        $(".btnFindGen").click(function () {
          var id = $(this).attr("data-id");
          var table = $(this).attr("data-table");
          findFeature(table, id);
        });
        $(".btnNavGen").click(function () {
          var id = $(this).attr("data-id");
          var table = $(this).attr("data-table");
          navFeature(table, id);
        });
        $(".btnEditGen").click(function () {
          var id = $(this).attr("data-id");
          var table = $(this).attr("data-table");
          editFeature(table, id);
        });
        $(".btnDeleteGen").click(function () {
          var id = $(this).attr("data-id");
          var table = $(this).attr("data-table");
          if (confirm("Are you sure that you want to delete this record?")) {
            deleteRecord(table, id, function () {
              populateFeatures(table);
              switch (table) {
                case "generic_point":
                  refreshGPt();
                  break;
                case "generic_line":
                  refreshGLn();
                  break;
                case "generic_poly":
                  refreshGPly();
                  break;
              }
            });
          }
        });
        openSubScreen("divFeatures");
      },
      error: function (xhr, status, error) {
        $("#features").html("ERROR: " + error);
        openSubScreen("divFeatures");
      },
    });
  } else {
    switch (tbl) {
      case "generic_point":
        var jsnFocus = jsnGPt;
        break;
      case "generic_line":
        var jsnFocus = jsnGLn;
        break;
      case "generic_poly":
        var jsnFocus = jsnGPly;
        break;
    }
    alert("Table: " + tbl + "\n" + JSON.stringify(jsnFocus));
  }
}

function populateCollect(tbl) {
  $("#gen_id").val("New");
  $("#gen_name").val("");
  $("#gen_descr").val("");
  $("#gen_lat").val(posCurrent.latitude.toFixed(5));
  $("#gen_lng").val(posCurrent.longitude.toFixed(5));
  $("#gen_geojson").val("");
  $("#hdrGenForm").html(tbl);
  $("#btnGenFormInsert").html("Insert into " + tbl);
  $("#btnGenFormInsert").show();
  $("#btnGenFormUpdate").hide();
  if (tbl == "generic_point") {
    $("#gen_form_pt").show();
    $("#gen_form_ln").hide();
  } else {
    $("#gen_form_pt").hide();
    $("#gen_form_ln").show();
  }
  openSubScreen("divGenForm");
}

function populateGeoJSON(tbl) {
  var arPoints = [];
  lyrVertices.eachLayer(function (lyr) {
    var jsn = lyr.toGeoJSON();
    arPoints.push(jsn.geometry.coordinates);
  });
  switch (tbl) {
    case "generic_line":
      var geojson = { type: "MultiLineString", coordinates: [arPoints] };
      break;
    case "generic_poly":
      arPoints.push(arPoints[0]);
      var geojson = { type: "MultiPolygon", coordinates: [[arPoints]] };
      break;
  }
  $("#gen_geojson").val(JSON.stringify(geojson));
}

function calculateAverage() {
  $("#divAverage").show();
  $("#btnScreenPoint").attr("disabled", true);
  $("#btnGPSAverage").attr("disabled", true);
  $("#btnGPSPoint").attr("disabled", true);
  $("#btnLayers").attr("disabled", true);
  dtAverageFinish = new Date(Date.now() + 60000);
  arAverage = [];
  intAverage = setInterval(function () {
    var dt = new Date();
    var seconds = (dtAverageFinish - dt) / 1000;
    if (seconds > 0) {
      $("#divAverage").html(seconds.toFixed(0) + "s remaining");
      $("#mode").html("AVERAGING (" + seconds.toFixed(0) + "s)");
      arAverage.push(posCurrent.latlng);
      populateAverage(arAverage);
    } else {
      $("#mode").html("Basic");
      $("#divAverage").hide();
      $("#btnScreenPoint").attr("disabled", false);
      $("#btnGPSAverage").attr("disabled", false);
      $("#btnGPSPoint").attr("disabled", false);
      $("#btnLayers").attr("disabled", false);
      openSubScreen("divGenForm");
      clearInterval(intAverage);
    }
  }, 1000);
}

function populateAverage(arLL) {
  var sumLat = 0;
  var sumLng = 0;
  arLL.forEach(function (ll, ndx) {
    sumLat += ll.lat;
    sumLng += ll.lng;
  });
  $("#gen_lat").val((sumLat / arLL.length).toFixed(6));
  $("#gen_lng").val((sumLng / arLL.length).toFixed(6));
}

function storeSettings() {
  var jsnSettings = {};
  jsnSettings.autolocate = $("#btnAutolocate").html();
  jsnSettings.numAutolocate = $("#numAutolocate").val();
  jsnSettings.breadcrumbs = $("#btnBreadcrumbs").html();
  jsnSettings.numBreadcrumbs = $("#numBreadcrumbs").val();
  jsnSettings.filter = $("#btnFilter").html();
  jsnSettings.numFilter = $("#numFilter").val();
  jsnSettings.numStream = $("#numStream").val();
  localStorage.jsnSettings = JSON.stringify(jsnSettings);
}

function refreshGPt() {
  if (isOnline) {
    $.ajax({
      url: "php/load_data.php",
      data: {
        tbl: "generic_point",
        where: "createdby='" + user.username + "'",
      },
      type: "POST",
      success: function (response) {
        if (response.substring(0, 5) == "ERROR") {
          alert(response);
        } else {
          jsnGPt = JSON.parse(response);
          localStorage.jsnGPt = response;
          if (lyrGPt) {
            ctlLayers.removeLayer(lyrGPt);
            lyrGPt.remove();
          }
          lyrGPt = L.geoJSON(jsnGPt, { pointToLayer: returnGPt }).addTo(mymap);
          ctlLayers.addOverlay(lyrGPt, "Generic Points");
        }
      },
      error: function (xhr, status, error) {
        alert("ERROR: " + error);
      },
    });
  } else {
    alert("No internet connection.");
    jsnGPt = JSON.parse(localStorage.jsnGPt);
    if (lyrGPt) {
      ctlLayers.removeLayer(lyrGPt);
      lyrGPt.remove();
    }
    lyrGPt = L.geoJSON(jsnGPt, { pointToLayer: returnGPt }).addTo(mymap);
    ctlLayers.addOverlay(lyrGPt, "Generic Points");
  }
}

function returnGPt(jsn, ll) {
  if (!jsn.properties.comments) {
    jsn.properties.comments = "";
  }
  return L.circleMarker(ll, { radius: 10, color: "orange" }).bindPopup(
    "<h4>" +
      jsn.properties.name +
      "</h4>" +
      jsn.properties.comments +
      "<br>Created by: " +
      jsn.properties.createdby
  );
}

function refreshGLn() {
  if (isOnline) {
    $.ajax({
      url: "php/load_data.php",
      data: { tbl: "generic_line", where: "createdby='" + user.username + "'" },
      type: "POST",
      success: function (response) {
        if (response.substring(0, 5) == "ERROR") {
          alert(response);
        } else {
          jsnGLn = JSON.parse(response);
          localStorage.jsnGLn = response;
          if (lyrGLn) {
            ctlLayers.removeLayer(lyrGLn);
            lyrGLn.remove();
          }
          lyrGLn = L.geoJSON(jsnGLn, {
            onEachFeature: processGLn,
            style: { color: "orange" },
          }).addTo(mymap);
          ctlLayers.addOverlay(lyrGLn, "Generic Lines");
        }
      },
      error: function (xhr, status, error) {
        alert("ERROR: " + error);
      },
    });
  } else {
    alert("No internet connection.");
    jsnGLn = JSON.parse(localStorage.jsnGLn);
    if (lyrGLn) {
      ctlLayers.removeLayer(lyrGLn);
      lyrGLn.remove();
    }
    lyrGLn = L.geoJSON(jsnGLn, {
      onEachFeature: processGLn,
      style: { color: "orange" },
    }).addTo(mymap);
    ctlLayers.addOverlay(lyrGLn, "Generic Lines");
  }
}

function processGLn(jsn, lyr) {
  if (!jsn.properties.comments) {
    jsn.properties.comments = "";
  }
  lyr.bindPopup(
    "<h4>" +
      jsn.properties.name +
      "</h4>" +
      jsn.properties.comments +
      "<br>Created by: " +
      jsn.properties.createdby
  );
}

function refreshGPly() {
  if (isOnline) {
    $.ajax({
      url: "php/load_data.php",
      data: { tbl: "generic_poly", where: "createdby='" + user.username + "'" },
      type: "POST",
      success: function (response) {
        if (response.substring(0, 5) == "ERROR") {
          alert(response);
        } else {
          jsnGPly = JSON.parse(response);
          localStorage.jsnGPly = response;
          if (lyrGPly) {
            ctlLayers.removeLayer(lyrGPly);
            lyrGPly.remove();
          }
          lyrGPly = L.geoJSON(jsnGPly, {
            onEachFeature: processGPly,
            style: { color: "orange" },
          }).addTo(mymap);
          ctlLayers.addOverlay(lyrGPly, "Generic Polygons");
        }
      },
      error: function (xhr, status, error) {
        alert("ERROR: " + error);
      },
    });
  } else {
    alert("No internet connection.");
    jsnGPly = JSON.parse(localStorage.jsnGPly);
    if (lyrGPly) {
      ctlLayers.removeLayer(lyrGPly);
      lyrGPly.remove();
    }
    lyrGPly = L.geoJSON(jsnGPly, {
      onEachFeature: processGPly,
      style: { color: "orange" },
    }).addTo(mymap);
    ctlLayers.addOverlay(lyrGPly, "Generic Polygons");
  }
}

function processGPly(jsn, lyr) {
  if (!jsn.properties.comments) {
    jsn.properties.comments = "";
  }
  lyr.bindPopup(
    "<h4>" +
      jsn.properties.name +
      "</h4>" +
      jsn.properties.comments +
      "<br>Created by: " +
      jsn.properties.createdby
  );
}

function findFeature(tbl, id) {
  $.ajax({
    url: "php/load_data.php",
    data: { tbl: tbl, where: "id=" + id },
    type: "POST",
    success: function (response) {
      if (response.substring(0, 5) == "ERROR") {
        $("#features").append(response);
      } else {
        stopAutolocate();
        var jsn = JSON.parse(response).features[0];
        if (lyrSearch) {
          lyrSearch.remove();
        }
        if (jsn.geometry.type == "Point") {
          var ll = L.latLng(
            jsn.geometry.coordinates[1],
            jsn.geometry.coordinates[0]
          );
          lyrSearch = L.circleMarker(ll, {
            radius: 15,
            color: "darkred",
            weight: 6,
          }).addTo(mymap);
          mymap.setView(ll, 17);
        } else {
          lyrSearch = L.geoJSON(jsn, {
            style: { color: "darkred", weight: 8 },
          }).addTo(mymap);
          mymap.fitBounds(lyrSearch.getBounds());
        }
        openSubScreen();
      }
    },
    error: function (xhr, status, error) {
      $("#features").append("ERROR: " + error);
    },
  });
}

function navFeature(tbl, id) {
  $.ajax({
    url: "php/load_data.php",
    data: { tbl: tbl, where: "id=" + id },
    type: "POST",
    success: function (response) {
      if (response.substring(0, 5) == "ERROR") {
        $("#features").append(response);
      } else {
        var jsn = JSON.parse(response).features[0];
        if (lyrNavTarget) {
          lyrNavTarget.remove();
        }
        if (jsn.geometry.type == "Point") {
          var ll = L.latLng(
            jsn.geometry.coordinates[1],
            jsn.geometry.coordinates[0]
          );
          lyrNavTarget = L.circleMarker(ll, {
            radius: 15,
            color: "darkviolet",
            weight: 6,
          }).addTo(mymap);
        } else {
          lyrNavTarget = L.geoJSON(jsn, {
            style: { color: "darkviolet", weight: 8 },
          }).addTo(mymap);
        }
        $("#mode").html("NAVIGATION (<span id='navData'></span>)");
        startAutolocate();
        openSubScreen();
      }
    },
    error: function (xhr, status, error) {
      $("#features").append("ERROR: " + error);
    },
  });
}

function editFeature(tbl, id) {
  $.ajax({
    url: "php/load_data.php",
    data: { tbl: tbl, where: "id=" + id },
    type: "POST",
    success: function (response) {
      if (response.substring(0, 5) == "ERROR") {
        $("#features").append(response);
      } else {
        var jsn = JSON.parse(response).features[0];
        $("#gen_id").val(jsn.properties.id);
        $("#gen_name").val(jsn.properties.name);
        $("#gen_descr").val(jsn.properties.descr);
        $("#hdrGenForm").html(tbl);
        $("#btnGenFormUpdate").html("Update " + tbl);
        $("#btnGenFormInsert").hide();
        $("#btnGenFormUpdate").show();
        if (tbl == "generic_point") {
          $("#gen_lat").val(jsn.geometry.coordinates[1].toFixed(5));
          $("#gen_lng").val(jsn.geometry.coordinates[0].toFixed(5));
          $("#gen_form_pt").show();
          $("#gen_form_ln").hide();
        } else {
          $("#gen_geojson").val(JSON.stringify(jsn.geometry));
          $("#gen_form_pt").hide();
          $("#gen_form_ln").show();
        }
        openSubScreen("divGenForm");
      }
    },
    error: function (xhr, status, error) {
      $("#features").append("ERROR: " + error);
    },
  });
}

function insertGenForm(tbl) {
  var jsn = returnFormData("inpGenForm");
  if (jsn.name == "") {
    alert("Please enter a name for this geometry");
  } else {
    jsn.tbl = tbl;
    delete jsn.id;
    if (tbl == "generic_point") {
      var geojson = {};
      geojson.type = "Point";
      geojson.coordinates = [Number(jsn.lng), Number(jsn.lat)];
      jsn.geojson = JSON.stringify(geojson);
    }
    delete jsn.lng;
    delete jsn.lat;
    insertRecord(jsn, function (response) {
      openSubScreen();
      switch (tbl) {
        case "generic_point":
          refreshGPt();
          break;
        case "generic_line":
          refreshGLn();
          lyrVertices.clearLayers();
          break;
        case "generic_poly":
          refreshGPly();
          lyrVertices.clearLayers();
          break;
      }
    });
  }
}

function updateGenForm(tbl) {
  var jsn = returnFormData("inpGenForm");
  if (jsn.name == "") {
    alert("Please enter a name for this geometry");
  } else {
    jsn.tbl = tbl;
    if (tbl == "generic_point") {
      var geojson = {};
      geojson.type = "Point";
      geojson.coordinates = [Number(jsn.lng), Number(jsn.lat)];
      jsn.geojson = JSON.stringify(geojson);
    }
    delete jsn.lng;
    delete jsn.lat;
    updateRecord(jsn, function (response) {
      openSubScreen();
      switch (tbl) {
        case "generic_point":
          refreshGPt();
          break;
        case "generic_line":
          refreshGLn();
          lyrVertices.clearLayers();
          break;
        case "generic_poly":
          refreshGPly();
          lyrVertices.clearLayers();
          break;
      }
    });
  }
}
