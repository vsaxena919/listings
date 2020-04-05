$siteURL = jQuery('#page').data("site-url");
$pinicon = jQuery('#singlepostmap').data('pinicon');
if($pinicon===""){
    $pinicon = $siteURL+"wp-content/themes/listingpro/assets/images/pins/pin.png";
}
if( jQuery('#singlepostmap').length !=0 ){
    $lat = jQuery('#singlepostmap').data("lat");
    $lan = jQuery('#singlepostmap').data("lan");
    "use strict";
    $mtoken = jQuery('#page').data("mtoken");
    $mtype = jQuery('#page').data("mtype");	
    if($mtype == 'mapbox' || $mtype == 'openstreet'){
        /* mapbox */
        L.HtmlIcon = L.Icon.extend({
            options: {
                /*
                html: (String) (required)
                iconAnchor: (Point)
                popupAnchor: (Point)
                */
            },
            initialize: function(options) {
                L.Util.setOptions(this, options);
            },
            createIcon: function() {
                var div = document.createElement('div');
                div.innerHTML = this.options.html;
                if (div.classList)
                    div.classList.add('leaflet-marker-icon');
                else
                    div.className += ' ' + 'leaflet-marker-icon';
                return div;
            },
            createShadow: function() {
                return null;
            }
        });
        if($mtoken != '' && $mtype == 'mapbox'){
            L.mapbox.accessToken = $mtoken;
            var map = L.mapbox.map('singlepostmap', $mapboxDesign)
                .setView([$lat,$lan], 17);
        }else if($mtype == 'openstreet'){
            var map = new L.Map('singlepostmap', '').setView(new L.LatLng($lat, $lan), 17);
		    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        }
        var markers = new L.MarkerClusterGroup();
        var markerLocation = new L.LatLng($lat, $lan); // London
        var CustomHtmlIcon = L.HtmlIcon.extend({
            options : {
                html : "<div class='lpmap-icon-shape pin '><div class='lpmap-icon-contianer'><img src='"+$pinicon+"'  /></div></div>",
            }
        });
        var customHtmlIcon = new CustomHtmlIcon();
        var marker = new L.Marker(markerLocation, {icon: customHtmlIcon}).bindPopup('').addTo(map);
        markers.addLayer(marker);
        map.fitBounds(markers.getBounds());
        map.scrollWheelZoom.disable();
        map.invalidateSize();
    }else{
        if($lan != '' && $lat != ''){
            function init(){var e={zoom:17,scrollwheel:!1,center:new google.maps.LatLng($lat,$lan),styles:[{featureType:"administrative",elementType:"labels.text.fill",stylers:[{color:"#444444"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2f2f2"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{saturation:-100},{lightness:45}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"simplified"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"#b7ecf0"},{visibility:"on"}]}]},l=document.getElementById("singlepostmap"),t=new google.maps.Map(l,e);new google.maps.Marker({position:new google.maps.LatLng($lat,$lan),icon:""+$pinicon+"",map:t,title:""})}google.maps.event.addDomListener(window,"load",init);
        }
    }
}else{
}