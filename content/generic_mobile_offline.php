<?php include("../includes/init.php");?>
<?php 
    if (logged_in()) {
        $username=$_SESSION['username'];
        if (!verify_user_group($pdo, $username, "Guests")) {
            set_msg("User '{$username}' does not have permission to view this page");
            redirect('../index.php');
        }
    } else {
        set_msg("Please log-in and try again");
        redirect('../index.php');
    } 
?>
<!DOCTYPE html>
<!--<html lang="en" manifest="generic_mobile_offline.appcache">-->
<html lang="en">
    <head>
        <meta charset="UTF-8">

        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Generic Mobile Offline">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title>Generic Mobile Offline</title>
        <link rel="stylesheet" href="src/leaflet.css">
        <link rel="stylesheet" href="src/css/bootstrap.min.css">
        <link rel="stylesheet" href="src/plugins/Leaflet.PolylineMeasure.css">
        <link rel="stylesheet" href="src/css/font-awesome.min.css">
        <link rel="stylesheet" href="src/plugins/leaflet.awesome-markers.css">
        <link rel="stylesheet" href="src/plugins/easy-button.css">
        <link rel="stylesheet" href="generic_mobile_resources/css_generic_mobile.css">
        
        <script src="src/leaflet.js"></script>
        <script src="src/jquery-3.3.1.min.js"></script>
        <script src="src/plugins/Leaflet.PolylineMeasure.js"></script>
        <script src="src/plugins/leaflet-providers.js"></script>
        <script src="src/plugins/leaflet.awesome-markers.min.js"></script>
        <script src="src/plugins/leaflet.pm.min.js"></script>
        <script src="src/plugins/easy-button.js"></script>
        <script src="src/plugins/togeojson.js"></script>
        <script src="src/plugins/leaflet.filelayer.js"></script>
        <script src="src/plugins/leaflet.geometryutil.js"></script>
        <script src="js/general_functions.js"></script>
        <script src="js/general_editing.js"></script>
        <script src="generic_mobile_resources/js_generic_mobile.js"></script>
        
    </head>
    <body>
        <div id="divHeader" class="col-xs-12 text-center">
            <div class="col-xs-12 text-center">
                <h4 id="mode"></h4>
                <div id="divStreamControls">
                    Last Location (<span class="time_since_fix">0</span>s)
                    <span id="btnStreamStop"> <i class="fa fa-stop-circle fa-2x pull-right"></i></span>
                    <span id="btnStreamPause"><i class="fa fa-pause-circle fa-2x pull-right"></i></span>
                    <span id="btnStreamPlay"> <i class="fa fa-play-circle fa-2x pull-right"></i></span>
                </div>
            </div>
            <div id="divNavData" class="col-xs-12 text-center">
                No Current Navigation Data (<span class="time_since_fix">0</span>s)
            </div>
        </div>
        <div id="divMap" class="col-xs-12">
            <div id=divCross><i class="fa fa-crosshairs fa-2x"></i></div>
        </div>
        <div id="divFooter" class="col-xs-12 text-center">
            <div class="btn-group btn-group-justified">
              <div class="btn-group">
                <button id="btnSync" class="btn btn-warning"><i class="fa fa-cloud-upload fa-2x"></i><span id='syncCount' class='badge badge-danger'>0</span></button>
              </div>
              <div class="btn-group">
                <button id="btnDlgInfo" class="btn btn-warning"><i class="fa fa-info fa-2x"></i></button>
              </div>
              <div class="btn-group">
                <button id="btnDlgLayers" class="btn btn-warning"><i class="fa fa-object-ungroup fa-2x"></i></button>
              </div>
              <div class="btn-group">
                <button id="btnDlgList" class="btn btn-warning"><i class="fa fa-list-alt fa-2x"></i></button>
              </div>
              <div class="btn-group">
                <button id="btnDlgSettings" class="btn btn-warning"><i class="fa fa-cog fa-2x"></i></button>
              </div>
            </div>
        </div>
        <div id="mapdiv" class="col-md-12"></div>
        <!-- The Modal -->
        <div id="dlgModal" class="modal">
              <div id="dlgSettings" class="modal-content col-xs-12">
                  <span class="btnCloseModal pull-right"><i class="fa fa-close fa-2x"></i></span>
                  <div id="settings" class="col-xs-12">
                      <div class="col-xs-12 text-center"><h2>Settings</h2></div>
                      <div class="col-xs-8"><h4>Autolocate: (<span id="numCurrentInterval"></span>)</h4></div>
                      <div class="col-xs-4">
                          <button id="btnAutolocate" class="btn btn-warning btn-block">Off</button>
                      </div>
                      <div id="sldrAutolocate">
                          <div class="col-xs-12">
                              <div class="col-xs-1">10s</div>
                              <div class="col-xs-8">
                                <input id="numPositionInterval" type="range" min="10" max="300" step="10" value="60">
                              </div>
                              <div class="col-xs-2">300s</div>
                          </div>
                      </div>
                      
                      <div class="col-xs-8"><h4>Breadcrumbs: (<span id="numCrumbQuantity">10</span>)</h4></div>
                      <div class="col-xs-4">
                          <button id="btnBreadcrumbs" class="btn btn-warning btn-block">Off</button>
                      </div>
                      <div id=sldrBreadcrumbs>
                          <div class="col-xs-12">
                              <div class="col-xs-1">5</div>
                              <div class="col-xs-8">
                                <input id="numCrumbs" type="range" min="5" max="100" step="5" value="10">
                              </div>
                              <div class="col-xs-2">100</div>
                          </div>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnZoomCustomNav" class="btn btn-primary btn-block">Zoom to Custom Layer</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnSetCustomNav" class="btn btn-primary btn-block">Navigate to Custom Layer</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnClearCustomLayer" class="btn btn-primary btn-block">Clear Custom Layer</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnClearCustomNav" class="btn btn-primary btn-block">Clear Navigation Target</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnClearSyncQue" class="btn btn-primary btn-block">Clear Sync Que</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnSaveSettings" class="btn btn-success btn-block">Save Settings</button>
                      </div>
                      <div class="col-xs-12">
                          <button id="btnLogout" class="btn btn-danger btn-block">Logout</button>
                      </div>
                  </div>
              </div>
              <div id="dlgList" class="modal-content col-xs-12">
                  <span class="btnCloseModal pull-right"><i class="fa fa-close fa-2x"></i></span>
                  <div class="col-xs-12 text-center"><h2 id="lstTitle"></h2></div>
                  <div id="list" class="col-xs-12"></div>
              </div>
              <div id="dlgLayers" class="modal-content col-xs-12">
                  <span class="btnCloseModal pull-right"><i class="fa fa-close fa-2x"></i></span>
                  <div class="col-xs-12 text-center"><h2>Layers</h2></div>
                  <div id="layers" class="col-xs-12">
                        <table class="table">
                            <tr><th>Name</th><th></th></tr>
                            <tr><td>gen_point</td><td><i id="gen_point_refresh" class="fa fa-refresh"></i></td><td><i id="gen_point_collect" class="fa fa-plus"></i></td><td><i id="gen_point_list" class="fa fa-list-alt"></i></td><td><i id="gen_point_download" class="fa fa-cloud-download"></i></td></tr>
                            <tr><td>gen_line</td><td><i id="gen_line_refresh" class="fa fa-refresh"></i></td><td><i id="gen_line_collect" class="fa fa-plus"></i></td><td><i id="gen_line_list" class="fa fa-list-alt"></i></td><td><i id="gen_line_download" class="fa fa-cloud-download"></i></td></tr>
                            <tr><td>gen_poly</td><td><i id="gen_poly_refresh" class="fa fa-refresh"></i></td><td><i id="gen_poly_collect" class="fa fa-plus"></i></td><td><i id="gen_poly_list" class="fa fa-list-alt"></i></td><td><i id="gen_poly_download" class="fa fa-cloud-download"></i></td></tr>
                        </table>
                  </div>
              </div>
              <div id="dlgInfo" class="modal-content col-xs-12">
                  <span class="btnCloseModal pull-right"><i class="fa fa-close fa-2x"></i></span>
                  <div id="info" class="col-xs-12">
                      <h4 class="text-center">Current Pos (<span class="time_since_fix"></span>s)</h4>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_cur_lat">Latitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_cur_lat" id="info_cur_lat" placeholder="Current Latitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_cur_lng">Longitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_cur_lng" id="info_cur_lng" placeholder="Current Longitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_cur_alt">Altitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_cur_alt" id="info_cur_alt" placeholder="Current Altitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_cur_tm">Time:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_cur_tm" id="info_cur_tm" placeholder="Current Time" readonly>
                            </div>
                      </div>
                      
                      <h4 class="text-center">Previous Pos</h4>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_prv_lat">Latitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_prv_lat" id="info_prv_lat" placeholder="Previous Latitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_prv_lng">Longitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_prv_lng" id="info_prv_lng" placeholder="Previous Longitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_prv_alt">Altitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_prv_alt" id="info_prv_alt" placeholder="Previous Altitude" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_prv_tm">Time:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_prv_tm" id="info_prv_tm" placeholder="Previous Time" readonly>
                            </div>
                      </div>

                      <h4 class="text-center">Difference</h4>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_dst">Distance:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_dst" id="info_dif_dst" placeholder="Distance" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_alt">Altitude:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_alt" id="info_dif_alt" placeholder="Altitude Change" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_bng">Bearing:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_bng" id="info_dif_bng" placeholder="Bearing" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_tm">Time:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_tm" id="info_dif_tm" placeholder="Time" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_vel">Velocity:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_vel" id="info_dif_vel" placeholder="Velocity" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="info_dif_clr">Climb Rate:</label>
                            <div class="col-xs-9">
                              <input type="text" class="form-control" name="info_dif_clr" id="info_dif_clr" placeholder="Climbing Rate" readonly>
                            </div>
                      </div>
                  </div>
              </div>
              <div id="dlgGenForm" class="modal-content col-xs-12">
                  <span class="btnCloseModal pull-right"><i class="fa fa-close fa-2x"></i></span>
                  <div id="gen_form" class="col-xs-12">
                      <h3 class="text-center"><span id="hdrGenForm" class="spnTable"></span></h3>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_id">ID:</label>
                            <div class="col-xs-9">
                                  <input type="text" class="form-control inpGenForm" name="id" id="gen_id" placeholder="ID" readonly>
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_name">Name:</label>
                            <div class="col-xs-9">
                                  <input type="text" class="form-control inpGenForm" name="name" id="gen_name" placeholder="Name">
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_comments">Comments:</label>
                            <div class="col-xs-9">
                                <textarea class="form-control inpGenForm" name="comments" id="gen_comments" placeholder="Comments"></textarea>
                            </div>
                      </div>
                  </div>
                  <div id="gen_form_point" class="col-xs-12">
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_lat">Latitude:</label>
                            <div class="col-xs-9">
                                  <input type="text" class="form-control inpGenForm" name="lat" id="gen_lat" placeholder="Latitude (30.12345)" >
                            </div>
                      </div>
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_lng">Longitude:</label>
                            <div class="col-xs-9">
                                  <input type="text" class="form-control inpGenForm" name="lng" id="gen_lng" placeholder="Longitude (-100.12345)">
                            </div>
                      </div>
                      <div class="btn-group btn-group-justified">
                          <div class="btn-group">
                            <button id="btnGPSPoint" class="btn btn-warning">Use Current Location</button>
                          </div>
                          <div class="btn-group">
                            <button id="btnScreenPoint" class="btn btn-warning">Choose Screen Location</button>
                          </div>
                      </div>
                  </div>
                  <div id="gen_form_line" class="col-xs-12">
                      <div class="form-group">
                            <label class="control-label col-xs-3" for="gen_geojson">GeoJSON:</label>
                            <div class="col-xs-9">
                                <textarea class="form-control inpGenForm" name="geojson" id="gen_geojson" placeholder="GeoJSON" readonly></textarea>
                            </div>
                      </div>
                      <div class="btn-group btn-group-justified">
                          <div class="btn-group">
                            <button id="btnStreamLine" class="btn btn-warning">Stream Line</button>
                          </div>
                          <div class="btn-group">
                            <button id="btnVertexLine" class="btn btn-warning">Vertex Line</button>
                          </div>
                      </div>
                  </div>
                  <div class="col-xs-12">
                      <button id="btnGenFormInsert" class="btn btn-success btn-block">Insert <span class="spnTable"></span></button>
                      <button id="btnGenFormUpdate" class="btn btn-warning btn-block">Update <span class="spnTable"></span></button>
                  </div>
              </div>
        </div>
        <script>
            var mymap;
            var posCurrent;
            var posPrevious;
            var lyrOSM;
            var lyrTopo;
            var lyrImagery;
            var lyrOutdoors;
            var lyrCustom;
            var lyrNavTarget;
            var lyrNavLine;
            var lyrCrumbs;
            var lyrVertices;
            var lyrGPt;
            var lyrGLn;
            var lyrGPly;
            var jsnGPt;
            var jsnGLn;
            var jsnGPly;
            var jsnLyrCrumbs={type:"FeatureCollection", features:[]};
            var jsnLyrVertices={type:"FeatureCollection", features:[]};
            var strCustomName;
            var mrkCurrentLocation;
            var ctlAttribute;
            var ctlScale;
            var ctlMeasure;
            var ctlLocate;
            var ctlCenter;
            var ctlLayers;
            var ctlFile;
            var objBasemaps;
            var objOverlays;
            var jsnSettings;
            var intPosition;
            var llCur;
            var llPrev;
            var strCurTime;
            var strPrvTime;
            if (localStorage.arSyncQue) {
                var arSyncQue=JSON.parse(localStorage.arSyncQue)
            } else {
                var arSyncQue=[];
                localStorage.arSyncQue=JSON.stringify(arSyncQue);
            }
            var dtLastPosition = new Date;
            var posCurrent;
            $(document).ready(function(){
                
                arSyncQue=arSyncQue.filter(function(itm){
                    return itm.result.substring(0,6)!=itm.type;
                });
                localStorage.arSyncQue=JSON.stringify(arSyncQue);
                // updateSyncCount();
                
                //  ********* Map Initialization ****************
                
                mymap = L.map('divMap', {attributionControl:false}).setView([19.42, -99.18], 13);
                mymap.locate();
                
                ctlAttribute = L.control.attribution({position:'bottomright'}).addTo(mymap);
                ctlAttribute.addAttribution('OSM');
                ctlAttribute.addAttribution('&copy; <a href="https://google.com">Jérôme ASSIE</a>');
                
                ctlScale = L.control.scale({position:'bottomright', metric:false, maxWidth:200}).addTo(mymap);

                //   *********** Layer Initialization **********
                
                lyrOSM = L.tileLayer.provider('OpenStreetMap.Mapnik');
                lyrTopo = L.tileLayer.provider('OpenTopoMap');
                lyrImagery = L.tileLayer.provider('Esri.WorldImagery');
                lyrOutdoors = L.tileLayer.provider('Thunderforest.Outdoors');
                mymap.addLayer(lyrOSM);
                lyrCrumbs = L.geoJSON(jsnLyrCrumbs, {style: {color:'green', weight:7, opacity:0.5}});
                lyrVertices = L.geoJSON(jsnLyrVertices, {style: {color:'red', weight:7, opacity:0.5}}).addTo(mymap);
                
                // ********* Setup Layer Control  ***************
                
                objBasemaps = {
                    "Open Street Maps": lyrOSM,
                    "Topo Map":lyrTopo,
                    "Imagery":lyrImagery,
                    "Outdoors":lyrOutdoors,
                };
                
                objOverlays = {
                };
                
                ctlLayers = L.control.layers(objBasemaps, objOverlays).addTo(mymap);
                
                refreshGPt(true);
                refreshGLn(true);
                refreshGPly(true);
                
                ctlMeasure = L.control.polylineMeasure({position:'topright'}).addTo(mymap);
                
                // ************ Location Events **************
                
                ctlLocate = L.easyButton( 'fa-bullseye fa-lg', function(){
                    mymap.locate();
                }).addTo(mymap);
                
                ctlCenter = L.easyButton( 'fa-crosshairs fa-lg', function(){
                    var center = mymap.getCenter();
                    var mode = $("#mode").html();
                    if (mode.substring(0,20)=="COLLECT CENTER POINT") {
                        $("#gen_lat").val(center.lat.toFixed(5));
                        $("#gen_lng").val(center.lng.toFixed(5));
                        $("#dlgModal").show();
                    } else if (mode.substring(0,5)=="COLLE") {
                        jsnCurrentCenter=L.marker(center).toGeoJSON();
                        jsnCurrentCenter.properties.timestamp=returnCurrentTimestamp();
                        lyrVertices.addLayer(L.geoJSON(jsnCurrentCenter, {pointToLayer:function(jsn, latlng){
                            return L.circle(center, {radius:20, color:'red'})   
                        }}));
                    } else {
                        alert("'"+mode.substring(0,20)+"'\nCenter lat: "+center.lat+"\nCenter lng: "+center.lng);
                    }
                        
                }).addTo(mymap);
                
                mymap.on('locationerror', function(e) {
                    console.log("Error: "+returnCurrentTimestamp());
                    console.log(e);
                })
                
                mymap.on('locationfound', function(e) {
                    posCurrent=e;
                    processLocation();
                    console.log("Success: "+returnCurrentTimestamp());
                    console.log(e);
                })
                
                //********** Initialize Filelayer control ***************

                ctlFile = L.Control.fileLayerLoad({position:'topright',
                    // Allows you to use a customized version of L.geoJson.
                    // For example if you are using the Proj4Leaflet leaflet plugin,
                    // you can pass L.Proj.geoJson and load the files into the
                    // L.Proj.GeoJson instead of the L.geoJson.
                    layer: L.geoJson,
                    // See http://leafletjs.com/reference.html#geojson-options
                    layerOptions: {pointToLayer: function(feature, latlng) {return L.circleMarker(latlng, {radius:7}); }},
                    // Add to map after loading (default: true) ?
                    addToMap: true,
                    // File size limit in kb (default: 1024) ?
                    fileSizeLimit: 4096,
                    // Restrict accepted file formats (default: .geojson, .kml, and .gpx) ?
                    formats: [
                        '.geojson',
                        '.kml',
                        '.gpx'
                    ]
                }).addTo(mymap);

                ctlFile.loader.on('data:loaded', function (e) {
                    e.layer.setStyle({color:'red', weight:7, opacity:0.5, fillColor:'tomato'});
                    lyrCustom=e.layer;
                    strCustomName=e.filename;
                    ctlLayers.addOverlay(lyrCustom, strCustomName);
                    localStorage.lyrCustom=JSON.stringify(lyrCustom.toGeoJSON());
                    if (confirm("Set "+strCustomName+" as navigation target?")) {
                        var jsn=lyrCustom.toGeoJSON().features[0];
                        var type=jsn.geometry.type;
                        jsn.properties.name=strCustomName;
                        if (lyrNavTarget) {
                            lyrNavTarget.remove();
                        }
                        if (type="Point") {
                            lyrNavTarget=L.geoJSON(jsn, {pointToLayer:function(jsn, latlng){
                                return L.circle(lyr.getLatLng(), {radius:30, color:'darkviolet'})}}).addTo(mymap);
                        } else {
                            lyrNavTarget = L.geoJSON(jsn, {style: {color:'darkviolet', weight:7, opacity:0.5, fillColor:'tomato'}}).addTo(mymap);
                        }
                        navModeOn();
                    }
                });
                
                if (localStorage.jsnSettings) {
                    jsnSettings=JSON.parse(localStorage.jsnSettings);
                } else { // set default settings if no localstorage
                    jsnSettings={};
                    jsnSettings.autolocate="Off";
                    jsnSettings.positioninterval="30";
                    jsnSettings.breadcrumbs="Off";
                    jsnSettings.crumbquantity="10";
                }
                
                // Populate settings dialog
                $("#btnAutolocate").html(jsnSettings.autolocate);
                if (jsnSettings.autolocate=="On") {
                    $("#sldrAutolocate").show();
                } else {
                    $("#sldrAutolocate").hide();
                }
                $("#numPositionInterval").val(jsnSettings.positioninterval);
                $("#numCurrentInterval").html(jsnSettings.positioninterval+"s");
                $("#btnBreadcrumbs").html(jsnSettings.breadcrumbs);
                if (jsnSettings.breadcrumbs=="On") {
                    $("#sldrBreadcrumbs").show();
                } else {
                    $("#sldrBreadcrumbs").hide();
                }
                $("#numCrumbs").val(jsnSettings.crumbquantity);
                $("#numCrumbQuantity").html(jsnSettings.crumbquantity);
                applySettings();
                
                setInterval(function(){
                    var dt=new Date;
                    var interval=((dt-dtLastPosition)/1000).toFixed(0);
                    $(".time_since_fix").html(interval);
                    // updateSyncCount();
                    if (isOnline()){
                        $("#syncCount").css("background-color", "greenyellow");
                        $("#syncCount").css("color", "red");
                        $("#btnSync").attr("disabled", false);
                    } else {
                        $("#syncCount").css("background-color", "white");
                        $("#syncCount").css("color", "#FF9933");
                        $("#btnSync").attr("disabled", true);
                    }
                });

                if (localStorage.lyrCustom) {
                    var jsnLyrCustom = JSON.parse(localStorage.lyrCustom);
                    strCustomName = jsnLyrCustom.features[0].properties.name;
                    lyrCustom = L.geoJSON(jsnLyrCustom, {style: {color:'red', weight:7, opacity:0.5, fillColor:'tomato'}}).addTo(mymap);
                    ctlLayers.addOverlay(lyrCustom, strCustomName);
                    mymap.fitBounds(lyrCustom.getBounds());
                } else {
                    alert("No layer found in local storage");
                }
                
            });
            
            $(".btnCloseModal").click(function(){
                $("#dlgModal").hide();
            })
            
// **************** Settings event handlers            
            
            $("#btnDlgSettings").click(function(){
                $(".modal-content").hide();
                $("#dlgSettings").show();
                $("#dlgModal").show(); 
            });
            
            $("#btnAutolocate").click(function(){
                if ($("#btnAutolocate").html()=="Off") {
                    $("#btnAutolocate").html("On");
                    $("#sldrAutolocate").show();
                } else {
                    $("#btnAutolocate").html("Off");
                    $("#sldrAutolocate").hide();
                }
            });
            
            $("#numPositionInterval").on('change', function(){
                $("#numCurrentInterval").html($("#numPositionInterval").val()+"s");
            });
            
            $("#btnBreadcrumbs").click(function(){
                if ($("#btnBreadcrumbs").html()=="Off") {
                    $("#btnBreadcrumbs").html("On");
                    $("#sldrBreadcrumbs").show();
                } else {
                    $("#btnBreadcrumbs").html("Off");
                    $("#sldrBreadcrumbs").hide();
                }
            });
            
            $("#numCrumbs").on('change', function(){
                $("#numCrumbQuantity").html($("#numCrumbs").val());
            });
            
            $("#numStreaming").on('change', function(){
                $("#spnStreaming").html($("#numStreaming").val()+"s");
            });
            
            $("#btnSaveSettings").click(function(){
                jsnSettings.autolocate=$("#btnAutolocate").html();
                jsnSettings.positioninterval=$("#numPositionInterval").val();
                jsnSettings.breadcrumbs=$("#btnBreadcrumbs").html();
                jsnSettings.crumbquantity=$("#numCrumbs").val();
                jsnSettings.streaming=$("#numStreaming").val();
                localStorage.jsnSettings=JSON.stringify(jsnSettings);
                applySettings();
                $("#dlgModal").hide();
            });
            
            
            $("#btnSetCustomNav").click(function(){
                if (lyrCustom) {
                    var jsn=lyrCustom.toGeoJSON().features[0];
                    var type=jsn.geometry.type;
                    jsn.properties.name=strCustomName;
                    if (lyrNavTarget) {
                        lyrNavTarget.remove();
                    }
                    if (type="Point") {
                        lyrNavTarget=L.geoJSON(jsn, {pointToLayer:function(jsn, latlng){
                            return L.circle(lyr.getLatLng(), {radius:30, color:'darkviolet'})}}).addTo(mymap);
                    } else {
                        lyrNavTarget = L.geoJSON(jsn, {style: {color:'darkviolet', weight:7, opacity:0.5, fillColor:'tomato'}}).addTo(mymap);
                    }
                    navModeOn();
                    $("#dlgModal").hide()
                } else {
                    alert("No custom layer exists");
                }
            });
            
            $("#btnClearCustomNav").click(function(){
                $("#mode").html("");
                navModeOff();
                $("#dlgModal").hide()
            });
            
            $("#btnClearSyncQue").click(function(){
                arSyncQue=[];
                localStorage.arSyncQue=JSON.stringify(arSyncQue);
                // updateSyncCount();
            });
            
            $("#btnClearCustomLayer").click(function(){
                if (lyrCustom) {
                    localStorage.removeItem('lyrCustom');
                    lyrCustom.remove();
                    lyrCustom={};
                    $("#mode").html("");
                    navModeOff();
                    $("#dlgModal").hide()
                } else {
                    alert("No custom layer exists");
                }
            });
            
            $("#btnZoomCustomNav").click(function(){
                if (lyrCustom) {
                    mymap.fitBounds(lyrCustom.getBounds());
                    $("#dlgModal").hide()
                } else {
                    alert("No custom layer exists");
                }

            });
            
            $("#btnLogout").click(function(){
                window.location="../logout.php";
            });
            
//   ********  Point List event handlers
            
            $("#btnDlgList").click(function(){
                showDlgList();
            });
            
//   ********  Layers event handlers
            
            $("#btnDlgLayers").click(function(){
                showModal("dlgLayers"); 
            });

//   ********  Info event handlers
            
            $("#btnDlgInfo").click(function(){
                showModal("dlgInfo"); 
            });
            
//   ********  Sync event handlers
            
            $("#btnSync").click(function(){
                arSyncQue=arSyncQue.filter(function(itm){
                    return itm.result.substring(0,6)!=itm.type;
                });
                localStorage.arSyncQue=JSON.stringify(arSyncQue);
                arSyncQue.forEach(function(item, ndx){
                    if (item.type != item.result.substring(0,6)){
                        switch(item.type) {
                            case "INSERT":
                                var script="php/insert_record.php";
                                break;
                            case "UPDATE":
                                var script="php/update_record.php";
                                break;
                            case "DELETE":
                                var script="php/delete_record.php";
                                break;
                        }
                        $.ajax({
                            url:script,
                            data:item.jsn,
                            type:"POST",
                            success: function(response){
                                arSyncQue[ndx].result=response;
                                localStorage.arSyncQue=JSON.stringify(arSyncQue);
                            },
                            error: function(xhr, status, error){
                                arSyncQue[ndx].result="ERROR: "+error;
                                localStorage.arSyncQue=JSON.stringify(arSyncQue);
                            } 
                        })
                    }
                });
            });
            
//  ****** Generic point event handlers
            $("#gen_point_refresh").click(function(){
                refreshGPt();
                $("#dlgModal").hide();
            });
            
            $("#gen_point_collect").click(function(){
                $(".spnTable").html("generic_point");
                $("#gen_id").val("New");
                $("#gen_name").val("");
                $("#gen_comments").val("");
                if (llCur) {
                    $("#gen_lat").val(llCur.lat.toFixed(5));
                    $("#gen_lng").val(llCur.lng.toFixed(5));
                } else {
                    $("#gen_lat").val("NA");
                    $("#gen_lng").val("NA");
                }
                $("#btnGenFormInsert").show();
                $("#btnGenFormUpdate").hide();
                showModal("dlgGenForm");
                $("#gen_form_point").show();
                $("#gen_form_line").hide();
            });
            
            $("#gen_point_list").click(function(){
                $("#lstTitle").html("Generic Points");
                if (isOnline()){
                    $.ajax({
                        url:"globals_resources/php_gen_list.php",
                        data:{tbl:'generic_point'},
                        type:'POST',
                        success:function(response){
                            createFeatureList(response);
                        },
                        error: function(xhr, status, error){
                           alert("ERROR: "+error);
                        } 
                    });
                } else {
                    var response = genListOffline(jsnGPt, "generic_point");
                    createFeatureList(response);
                }
                showModal("dlgList");
            });
            
            $("#gen_point_download").click(function(){
                var fn = "generic_points_"+returnCurrentDate()+".geojson";
                var content = JSON.stringify(lyrGPt.toGeoJSON());
                download(fn, content);
            });
            
            $("#btnGPSPoint").click(function(){
                if (llCur) {
                    alert("Updating lat to "+llCur.lat.toFixed(5)+" and lng to "+llCur.lng);
                    $("#gen_lat").val(llCur.lat.toFixed(5));
                    $("#gen_lng").val(llCur.lng.toFixed(5));
                } else {
                    alert("No position available");
                    $("#gen_lat").val("NA");
                    $("#gen_lng").val("NA");
                }
            });
            
            $("#btnScreenPoint").click(function(){
                $("#dlgModal").hide();
                $("#mode").html("COLLECT CENTER POINT <i class='fa fa-crosshairs'></i>");
                navModeOff();
                autoLocateOff();
            })
            
// ************  Generic Line event handlers
            $("#gen_line_refresh").click(function(){
                refreshGLn();
                $("#dlgModal").hide();
            });
            
            $("#gen_line_collect").click(function(){
                collectLine("generic_line");
                $(".spnTable").html("generic_line");
            });
            
            $("#gen_line_list").click(function(){
                $("#lstTitle").html("Generic Lines");
                if (isOnline()){
                    $.ajax({
                        url:"globals_resources/php_gen_list.php",
                        data:{tbl:'generic_line'},
                        type:'POST',
                        success:function(response){
                            createFeatureList(response);
                        },
                        error: function(xhr, status, error){
                           alert("ERROR: "+error);
                        } 
                    });
                } else {
                    var response = genListOffline(jsnGLn, "generic_line");
                    createFeatureList(response);
                }
                $("#dlgList").show();
                $("#dlgModal").show(); 
            });
            
            $("#gen_line_download").click(function(){
                var fn = "generic_lines_"+returnCurrentDate()+".geojson";
                var content = JSON.stringify(lyrGLn.toGeoJSON());
                download(fn, content);
            });
            
// ************  Generic Polygon event handlers
            $("#gen_poly_refresh").click(function(){
                refreshGPly();
                $("#dlgModal").hide();
            });
            
            $("#gen_poly_collect").click(function(){
                collectLine("generic_poly");
                $(".spnTable").html("generic_poly");
            });
            
            $("#gen_poly_list").click(function(){
                $("#lstTitle").html("Generic Polygons");
                if (isOnline()){
                    $.ajax({
                        url:"globals_resources/php_gen_list.php",
                        data:{tbl:'generic_poly'},
                        type:'POST',
                        success:function(response){
                            createFeatureList(response);
                        },
                        error: function(xhr, status, error){
                           alert("ERROR: "+error);
                        } 
                    });
                } else {
                    var response = genListOffline(jsnGPly, "generic_poly");
                    createFeatureList(response);
                }
                $("#dlgList").show();
                $("#dlgModal").show(); 
            });
            
            $("#gen_poly_download").click(function(){
                var fn = "generic_polys_"+returnCurrentDate()+".geojson";
                var content = JSON.stringify(lyrGPly.toGeoJSON());
                download(fn, content);
            });
            
// ************  Generic event handlers
            $("#btnGenFormInsert").click(function(){
                jsn=returnFormData("inpGenForm");
                if (jsn.name=="") {
                    alert("Please enter a name for this geometry");
                } else {
                    var tbl=$("#hdrGenForm").html();
                    jsn.tbl=tbl;
                    delete jsn.id;
                    switch (tbl) {
                        case "generic_point": {
                            var geojson={};
                            geojson.type="Point";
                            geojson.coordinates=[Number(jsn.lng), Number(jsn.lat)];
                            jsn.geojson=JSON.stringify(geojson);                        }
                            break;
                    }
                    delete jsn.lng;
                    delete jsn.lat;
                    if (isOnline()){
                        insertRecord(jsn, function(response){
                            $("#dlgModal").hide();
                            switch (tbl) {
                                case "generic_point": {
                                    refreshGPt();
                                    continueNav();
                                    break;
                                }
                                case "generic_line": {
                                    refreshGLn();
                                    lyrVertices.clearLayers();               
                                    continueNav();
                                    break;
                                }
                                case "generic_poly": {
                                    refreshGPly();
                                    lyrVertices.clearLayers();               
                                    continueNav();
                                    break;
                                }
                            }
                        });
                    } else {
                        insertRecordOffline(jsn);
                        $("#dlgModal").hide();
                        lyrVertices.clearLayers();
                        continueNav();
                    }
                }
            });
            
            function continueNav(){
                if (confirm("Continue navigation?")) {
                    navModeOn();
                } else {
                    $("#mode").html("");
                    navModeOff();
                }
            }

            $("#btnGenFormUpdate").click(function(){
                jsn=returnFormData("inpGenForm");
                if (jsn.name=="") {
                    alert("Please enter a name for this geometry");
                } else {
                    var tbl=$("#hdrGenForm").html();
                    jsn.tbl=tbl;
                    var geojson={};
                    switch (tbl) {
                        case "generic_point": 
                            geojson.type="Point";
                            geojson.coordinates=[Number(jsn.lng), Number(jsn.lat)];
                            delete jsn.lng;
                            delete jsn.lat;
                            break;
                    }
                    jsn.geojson=JSON.stringify(geojson);
                    if (isOnline()){
                        updateRecord(jsn, function(response){
                            $("#dlgModal").hide();
                            switch (tbl) {
                                case "generic_point":
                                    var ll=L.latLng([geojson.coordinates[1],geojson.coordinates[0]]);
                                    refreshGPt();
                                    mymap.setView(ll, 17)
                                    break;
                            }
                        });
                    } else {
                        updateRecordOffline(jsn);
                        $("#dlgModal").hide();
                        switch (tbl) {
                            case "generic_point":
                                var ll=L.latLng([geojson.coordinates[1],geojson.coordinates[0]]);
                                mymap.setView(ll, 17)
                                break;
                        }
                    }
                }
            });
            
            $("#btnStreamLine").click(function(){
                $("#dlgModal").hide();
                $("#mode").html("COLLECT LINE STREAM");
                $("#divStreamControls").show()
                $("#btnStreamPlay").hide();
                $("#btnStreamPause").show();
                $("#btnStreamStop").show();
                navModeOff();
                autoLocateOn();
                mymap.locate()();
            })
            
            $("#btnVertexLine").click(function(){
                $("#dlgModal").hide();
                $("#mode").html("COLLECT LINE VERTEX");
                $("#divStreamControls").show()
                $("#btnStreamPlay").hide();
                $("#btnStreamPause").hide();
                $("#btnStreamStop").show();
                navModeOff();
                autoLocateOff();
            })
            
            $("#btnStreamPlay").click(function(){
                $("#mode").html("COLLECT LINE STREAM");
                $("#btnStreamPlay").hide();
                $("#btnStreamPause").show();
                $("#btnStreamStop").show();
                mymap.locate();
            });
            
            $("#btnStreamPause").click(function(){
                $("#mode").html("PAUSE LINE STREAM");
                $("#btnStreamPlay").show();
                $("#btnStreamPause").hide();
                $("#btnStreamStop").show();
            });
            
            $("#btnStreamStop").click(function(){
                $("#mode").html("");
                $("#divStreamControls").hide();
                showModal("dlgGenForm");
                populateGen_geojson($("#hdrGenForm").html());
            });
            
        </script>
    </body>
</html>
