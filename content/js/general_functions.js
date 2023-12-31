            //  ***********  General Functions *********
            
            function LatLngToArrayString(ll) {
                return "["+ll.lat.toFixed(5)+", "+ll.lng.toFixed(5)+"]";
            }
            
            function returnLayerByAttribute(tbl,fld,val,callback) {
                var whr=fld+"='"+val+"'";
                $.ajax({
                    url:'php/load_data.php',
                    data: {tbl:tbl, where:whr},
                    type: 'POST',
                    success: function(response){
                        if (response.substr(0,5)=="ERROR") {
                            alert(response);
                            callback(false);
                        } else {
                            var jsn = JSON.parse(response);
                            var lyr = L.geoJSON(jsn);
                            var arLyrs=lyr.getLayers();
                            if (arLyrs.length>0) {
                                callback(arLyrs[0]);
                            } else {
                                callback(false);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("ERROR: "+error);
                        callback(false);
                    }
                });
            }
            
            function returnRecordByID(tbl, id, callback) {
                var whr="id='"+id+"'";
                $.ajax({
                    url:'php/load_data.php',
                    data: {tbl:tbl, where:whr, spatial:"NO"},
                    type: 'POST',
                    success: function(response){
                        if (response.substr(0,5)=="ERROR") {
                            alert(response);
                            callback(false);
                        } else {
                            var jsn = JSON.parse(response);
                            if (jsn.length>0) {
                                callback(jsn[0].properties);
                            } else {
                                callback(false);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("ERROR: "+error);
                        callback(false);
                    }
                });
            }
            
            function returnLayersByAttribute(lyr,att,val) {
                var arLayers = lyr.getLayers();
                var arMatches = [];
                for (i=0;i<arLayers.length-1;i++) {
                    var ftrVal = arLayers[i].feature.properties[att];
                    if (ftrVal==val) {
                        arMatches.push(arLayers[i]);
                    }
                }
                if (arMatches.length) {
                    return arMatches;
                } else {
                    return false;
                }
            }
            
            function testLayerAttribute(ar, val, att, fg, err, btn) {
                if (ar.indexOf(val)<0) {
                    $(fg).addClass("has-error");
                    $(err).html("**** "+att+" NOT FOUND ****");
                    $(btn).attr("disabled", true);
                } else {
                    $(fg).removeClass("has-error");
                    $(err).html("");
                    $(btn).attr("disabled", false);
                }
            }
            
            function returnLength(arLL) {
                var total=0;
                
                for (var i=1;i<arLL.length;i++) {
                    total = total + arLL[i-1].distanceTo(arLL[i]);
                }
                
                return total;
                
            }
            
            function returnMultiLength(arArLL) {
                var total=0;
                
                for (var i=0; i<arArLL.length;i++) {
                    total = total + returnLength(arArLL[i]);
                }
                
                return total;
            }
            
            function stripSpaces(str) {
                return str.replace(/\s+/g, '');
            }
            
            function returnCurrentDate(){
                var currentDate = new Date();
                
                var currentDay = currentDate.getDate();
                if (currentDay<10){currentDay="0"+currentDay}
                
                var currentMonth = currentDate.getMonth()+1;
                if (currentMonth<10){currentMonth="0"+currentMonth};
                
                var currentYear = currentDate.getFullYear();
                
                return currentYear+"-"+currentMonth+"-"+currentDay;
            }
            
            function returnCurrentTimestamp(){
                var currentDate = new Date();
                
                var currentDay = currentDate.getDate();
                if (currentDay<10){currentDay="0"+currentDay}
                
                var currentMonth = currentDate.getMonth()+1;
                if (currentMonth<10){currentMonth="0"+currentMonth};
                
                var currentYear = currentDate.getFullYear();
                
                var currentHour = currentDate.getHours();
                if (currentHour<1){
                    currentHour="00"
                } else if (currentHour<10){
                    currentHour="0"+currentHour
                };

                var currentMinute = currentDate.getMinutes();
                if (currentMinute<1){
                    currentMinute="00"
                } else if (currentMinute<10){
                    currentMinute="0"+currentMinute
                };

                var currentSecond = currentDate.getSeconds();
                if (currentSecond<1){
                    currentSecond="00"
                } else if (currentSecond<10){
                    currentSecond="0"+currentSecond
                };

                return currentYear+"-"+currentMonth+"-"+currentDay+" "+currentHour+":"+currentMinute+":"+currentSecond;
            }

             function returnTimeFromUTC(val){
                var currentDate = new Date(val);
                
                var currentHour = currentDate.getHours();
                if (currentHour<10){currentHour="0"+currentHour}
                
                var currentMinute = currentDate.getMinutes();
                if (currentMinute<10){currentMinute="0"+currentMinute}
                
                var currentSecond = currentDate.getSeconds();
                if (currentSecond<10){currentSecond="0"+currentSecond}
                
                return currentHour+":"+currentMinute+":"+currentSecond;
            }
            
           function returnFormData(inpClass) {
                var objFormData={};
                $("."+inpClass).each(function(){
                    objFormData[this.name]=this.value;
                });
                return objFormData;
            }
            
           function changeOptions(element, tbl, fld) {
                $.ajax({
                    url:'php/distinct_options.php',
                    data:{tbl:tbl, fld:fld},
                    type:'POST',
                    success:function(response){
                        if (response.substring(0,5)=="ERROR") {
                            alert(response);
                        } else {
                            $("#"+element).html(response);
                        }
                    },
                    error:function(xhr, status, error){
                        $("#tableData").html("ERROR: "+error);
                        $("#dlgModal").show();
                    }
                });
            }
            
            function isShowing(element){
                if ($("#"+element).css("display")=="none"){
                    return false;
                } else {
                    return true;
                }
            }

            function download(filename, text) {
                var element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
                element.setAttribute('download', filename);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            }

            function isMobile(){
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
                    return true;
                } else {
                    return false;
                }
            }
            
            function isOnline(){
                return navigator.onLine;
            }
            
