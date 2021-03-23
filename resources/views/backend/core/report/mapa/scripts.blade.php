<script>
    var map = L.map('map-content', {
        minZoom: 3,
        maxZoom: 18,
        fullscreenControl: true,
    }).setView(["-23.533773", "-46.625290"], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var optionsPopup = {
         maxWidth: 300,
         className: 'markerPopup'
    }

    var defaultIcon = L.icon({
        iconUrl: '/img/backend/mapa/pin.png',
        iconSize:     [36, 36],
        iconAnchor:   [18, 18],
        popupAnchor:  [0, -20]
    });

    var lastPopup;
    function makeMarker(v) {
        var marker = L.marker([v.lat, v.lng], { icon:defaultIcon, draggable:false});

        if (v.nome) {
            marker.on('mouseover', function(e) {
                if (lastPopup && lastPopup.isOpen() && lastPopup.getLatLng().equals(L.latLng(v.lat, v.lng))) {
                    return;
                }

                lastPopup = L.popup(optionsPopup)
                    .setLatLng(e.latlng)
                    .setContent(makePopup(v))
                    .openOn(map);
            });
        }

        // marker.bindPopup(makePopup(v), optionsPopup);
        return marker;
    }

    function makePopup(v) {
        var popup = '';
        popup += '<div>'
        popup += ' <div class="content-text">';
        popup += '  <div class="text-1">'+v.nome+'</div>';
        popup += '  <div class="text-2">'+v.produtor_nome+'</div>';
        if (v.socios) {
            popup += '  <div class="text-3">'+v.socios+'</div>';
        }
        popup += ' </div>'
        popup += ' <div class="content-btn">';
        popup += '  <a class="btn btn-primary" href="/admin/produtor/'+v.produtor_id+'/dashboard" target="_blank">Ver Perfil</a>'
        popup += ' </div>'
        popup += '</div>';
        return popup;
    }

    var markerClusterGroup;
    function callbackMapa(rs) {
        if (markerClusterGroup) {
            markerClusterGroup.clearLayers();
        }

        markerClusterGroup = L.markerClusterGroup({
            maxClusterRadius:40,
            spiderLegPolylineOptions: {
                weight: 1,
                color: '#08885b'
            }
        });
        for(var i=0; i < rs.length; i++) {
            var item = rs[i];

            if (isNaN(item.lat) || isNaN(item.lng)) {
                console.log("Unidade produtiva inválida", item);
                continue;
            }

            markerClusterGroup.addLayer(makeMarker(item));
        }

        map.addLayer(markerClusterGroup);
    }

    function submitFilter(ignoreExpand) {
        if (!ignoreExpand && $("#card-filter").hasClass('is-expand')) {
            $("#card-filter").addClass("hide");
        }

        var form = $("#form-filter");

        //Força validação p/ mostrar erros na tela
        if (form[0].checkValidity() == false) {
            form[0].dispatchEvent(new Event('submit'));
            return;
        }

        $("#form-submit").addClass("loading");
        $(".map-lat-lng").addClass("loading");

        //Chamada genérica do botão "Filtrar"
        var data = form.serializeArray();
        console.log(form.serialize());

        $.ajax({
            type: "POST",
            url: form.attr("action"),
            data: data,
            dataType: "json",
            //headers:{
            //    'Content-Type': 'application/json',
            //    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content"),
            //},
            xhrFields: {
                withCredentials: true
            },
            // credentials: "same-origin",
            success: function(data) {
                $("#form-submit").removeClass("loading");
                $(".map-lat-lng").removeClass("loading");
                callbackMapa(data); //TODO ficar genérico
            },
            error: function(err) {
                $("#form-submit").removeClass("loading");
                $(".map-lat-lng").removeClass("loading");

                let message = "Erro ao enviar os dados, tente novamente.";
                if (err && err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }

                toastr.error(message);
            }
        });
    }

    //Init
    setTimeout(submitFilter, 500, true);

    //Submit form
    $(document).ready(function() {
        $("#card-filter #form-submit").click(function(evt) {
            evt.preventDefault();
            submitFilter(false);
        });
    });
</script>

<script>
    $(document).ready(function() {
        return; //Testar melhor p/ ver se atende
        $.ajax ({
            url: 'admin/api/regioes',
            datatype: "json",
            success: function (rs) {
                var overlayMaps = {};

                rs.regioes.forEach(function(v) {
                    //id, nome, poligono

                    var customLayer = L.geoJson(null, {
                        style:{
                            // color: "#ff7800",
                            weight: 1,
                            opacity: 0.65
                        },
                    });
                    customLayer.bindTooltip(v.nome, {
                        permanent:false,
                        opacity:.8
                    });

                    omnivore.wkt.parse(v.poligono, null, customLayer);

                    overlayMaps[v.nome] = customLayer;
                });

                L.control.layers(null, overlayMaps).addTo(map);
            }
        });
    })
</script>
