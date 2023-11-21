            function styleClientLinears(json) {
                var att = json.properties;
                switch (att.type) {
                    case 'Pipeline':
                        return {color:'peru'};
                        break;
                    case 'Flowline':
                        return {color:'navy'};
                        break;
                    case 'Flowline, est.':
                        return {color:'navy', dashArray:"5,5"};
                        break;
                    case 'Electric Line':
                        return {color:'darkgreen'};
                        break;
                    case 'Access Road - Confirmed':
                        return {color:'darkred'};
                        break;
                    case 'Access Road - Estimated':
                        return {color:'darkred', dashArray:"5,5"};
                        break;
                    case 'Extraction':
                        return {color:'indigo'};
                        break;
                    default:
                        return {color:'darkgoldenrod'}
                }
            }
            
            function processClientLinears(json, lyr) {
                var att = json.properties;
                lyr.bindTooltip("<h4>Linear Project: "+att.project+"</h4>Type: "+att.type+"<br>ROW Width: "+att.row_width+"<br>Length: "+returnMultiLength(lyr.getLatLngs()).toFixed(0));
                arProjectIDs.push(att.project.toString());
            }
            
            function findProject(val){
                returnLayerByAttribute("dj_linear",'project',val, function(lyr){
                    if (lyr) {
                        if (lyrSearch) {
                            lyrSearch.remove();
                        }
                        lyrSearch = L.geoJSON(lyr.toGeoJSON(), {style:{color:'red', weight:10, opacity:0.5}}).addTo(mymap);
                        mymap.fitBounds(lyr.getBounds().pad(1));
                        var att = lyr.feature.properties;
                        $("#linear_id").val(att.id);
                        $("#linear_type").val(att.type);
                        $("#linear_row_width").val(att.row_width);
                        $("#linear_geojson").val(JSON.stringify(lyr.feature.geometry));
                        $("#projectMetadata").html("Created "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by "+att.modifiedby);
                        $(".inpLinear").attr("disabled", true);
                        $("#LinearGeojson").hide();
                        $("#formProject").show();
                        
                        $.ajax({
                            url:'djbasin_resources/php_affected_constraints.php',
                            data:{id:val},
                            type:'POST',
                            success:function(response){
                                $("#divProjectAffected").html(response);
                            },
                            error:function(xhr, status, error){
                                $("#divProjectAffected").html("ERROR: "+error);
                            }
                        });
                        $("#divProjectError").html("");

                        $(".btnSurveys").hide();
                        $("#btnEditLinear").show();
                        $("#btnDeleteLinear").show();
                    } else {
                        $("#divProjectError").html("**** Project ID not found ****");
                    }
                });
                
            }
            
            function refreshLinears(whr) {
                if (whr) {
                    var objData={tbl:'dj_linear', flds:"id, type, row_width, project", where:whr}
                } else {
                    var objData={tbl:'dj_linear', flds:"id, type, row_width, project"}
                }
                $.ajax({url:'php/load_data.php', 
                    data: objData,
                    type: 'POST',
                    success: function(response){
                        if (response.substring(0,5)=="ERROR"){
                            alert(response);
                        } else {
                            arProjectIDs=[];
                            jsnLinears = JSON.parse(response);
                            if (lyrClientLines) {
                                ctlLayers.removeLayer(lyrClientLines);
                                lyrClientLines.remove();
                                lyrClientLinesBuffer.remove();
                            }
                            lyrClientLinesBuffer = L.featureGroup();
                            lyrClientLines = L.geoJSON(jsnLinears, {style:styleClientLinears, onEachFeature:processClientLinears}).addTo(mymap);
                            ctlLayers.addOverlay(lyrClientLines, "Linear Projects");
                            arProjectIDs.sort(function(a,b){return a-b});
                            $("#txtFindProject").autocomplete({
                                source:arProjectIDs
                            });
                            refreshLinearBuffers(whr);
                        }
                    }, 
                    error: function(xhr, status, error){
	                   alert("ERROR: "+error);
                    } 
                });
            }
                
            function refreshLinearBuffers(whr) {
                if (whr) {
                    var objData={tbl:'dj_linear', flds:"id, type, row_width, project", where:whr, distance:"row_width"}
                } else {
                    var objData={tbl:'dj_linear', flds:"id, type, row_width, project", distance:"row_width"}
                }
                $.ajax({url:'php/load_data.php', 
                    data: objData,
                    type: 'POST',
                    success: function(response){
                        if (response.substring(0,5)=="ERROR"){
                            alert(response);
                        } else {
                            jsnLinearBuffers = JSON.parse(response);
                            if (lyrClientLinesBuffer) {
                                lyrClientLinesBuffer.remove();
                            }
                            lyrClientLinesBuffer = L.geoJSON(jsnLinearBuffers, {style:{color:'grey', dashArray:'5,5', fillOpacity:0}}).addTo(mymap);
                            lyrClientLines.bringToFront();
                        }
                    }, 
                    error: function(xhr, status, error){
	                   alert("ERROR: "+error);
                    } 
                });
            }
                
