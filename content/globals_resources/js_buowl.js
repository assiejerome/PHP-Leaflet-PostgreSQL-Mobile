            function styleBUOWL(json){
                var att = json.properties;
                switch (att.hist_occup){
                    case 'Yes':
                        return {color:'deeppink', fillColor:'yellow'};
                        break;
                    case 'Undetermined':
                        return {color:'yellow'};
                        break;
                }
            }
            
            function processBUOWL(json, lyr){
                var att = json.properties;
                lyr.bindTooltip("<h4>Habitat ID: "+att.habitat_id+"</h4>Historically Occupied: "+att.hist_occup+"<br>Status: "+att.recentstatus);
                arHabitatIDs.push(att.habitat_id.toString())
            }
            
            function findBUOWL(val) {
                returnLayerByAttribute("dj_buowl",'habitat_id',val, function(lyr){
                    if (lyr) {
                        if (lyrSearch) {
                            lyrSearch.remove();
                        }
                        lyrSearch = L.geoJSON(lyr.toGeoJSON(), {style:{color:'red', weight:10, opacity:0.5, fillOpacity:0}}).addTo(mymap);
                        mymap.fitBounds(lyr.getBounds().pad(1));
                        var att = lyr.feature.properties;
                        $("#buowl_id").val(att.id);
                        $("#buowl_habitat").val(att.habitat);
                        $("#buowl_hist_occup").val(att.hist_occup);
                        $("#buowl_recentstatus").val(att.recentstatus);
                        $("#buowl_lastsurvey").val(att.lastsurvey);
                        $("#buowl_geojson").val(JSON.stringify(lyr.feature.geometry));
                        $("#BUOWLmetadata").html("Created "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by "+att.modifiedby);
                        $(".inpBUOWL").attr("disabled", true);
                        $("#BUOWLgeojson").hide();
                        $("#formBUOWL").show();
                        
                        $.ajax({
                            url:'djbasin_resources/php_affected_projects.php',
                            data:{tbl:'dj_buowl', distance:300, fld:'habitat_id', id:val},
                            type:'POST',
                            success:function(response){
                                $("#divBUOWLaffected").html(response);
                            },
                            error:function(xhr, status, error){
                                $("#divBUOWLaffected").html("ERROR: "+error);
                            }
                        });
                        
                        $("#divBUOWLError").html("");

                        $(".btnSurveys").hide();
                        $("#btnBUOWLsurveys").show();
                        $("#btnEditBUOWL").show();
                        $("#btnDeleteBUOWL").show();

                     } else {
                        $("#divBUOWLError").html("**** Habitat ID not found ****");
                    }
                });
            }
                                
            function refreshBUOWL(whr) {
                if (whr) {
                    var objData={tbl:'dj_buowl', flds:"id, habitat_id, habitat, recentstatus, hist_occup", where:whr};
                } else {
                    var objData={tbl:'dj_buowl', flds:"id, habitat_id, habitat, recentstatus, hist_occup"};
                }
                $.ajax({url:'php/load_data.php', 
                    data: objData,
                    type: 'POST',
                    success: function(response){
                        if (response.substring(0,5)=="ERROR"){
                            alert(response);
                        } else {
                            arHabitatIDs=[];
                            jsnBUOWL = JSON.parse(response);
                            if (lyrBUOWL) {
                                ctlLayers.removeLayer(lyrBUOWL);
                                lyrBUOWL.remove();
                                lyrBUOWLbuffer.remove();
                            }
                            lyrBUOWL = L.geoJSON(jsnBUOWL, {style:styleBUOWL, onEachFeature:processBUOWL}).addTo(mymap);
                            ctlLayers.addOverlay(lyrBUOWL, "Burrowing Owl Habitat");
                            arHabitatIDs.sort(function(a,b){return a-b});
                            $("#txtFindBUOWL").autocomplete({
                                source:arHabitatIDs
                            });
                            refreshBUOWLbuffer(whr);
                        }
                    }, 
                    error: function(xhr, status, error){
	                   alert("ERROR: "+error);
                    } 
                });
            }
                
            function refreshBUOWLbuffer(whr) {
                if (whr) {
                    var objData={tbl:'dj_buowl', flds:"id, habitat_id, habitat, recentstatus, hist_occup", where:whr, distance:300};
                } else {
                    var objData={tbl:'dj_buowl', flds:"id, habitat_id, habitat, recentstatus, hist_occup", distance:300};
                }
                $.ajax({url:'php/load_data.php', 
                    data: objData,
                    type: 'POST',
                    success: function(response){
                        if (response.substring(0,5)=="ERROR"){
                            alert(response);
                        } else {
                            jsnBUOWLbuffer = JSON.parse(response);
                            if (lyrBUOWLbuffer) {
                                lyrBUOWLbuffer.remove();
                            }
                            lyrBUOWLbuffer = L.geoJSON(jsnBUOWLbuffer, {style:{color:'yellow', dashArray:'5,5', fillOpacity:0}}).addTo(mymap);
                            lyrBUOWL.bringToFront();
                        }
                    }, 
                    error: function(xhr, status, error){
	                   alert("ERROR: "+error);
                    } 
                });
            }
                
