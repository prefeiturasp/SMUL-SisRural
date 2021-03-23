<script>
        setTimeout(function() {
            var defaultLat = "{{$lat}}";
            var defaultLng = "{{$lng}}";

            var map = L.map('map-content').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var marker = L.marker([defaultLat, defaultLng], { title: "Marker", draggable:false }).addTo(map);

            // Cliente pediu para retirar essa função
            // map.addEventListener('drag', function(e) {
            //     var center = map.getCenter();
            //     setLatLng(center.lat, center.lng)
            // })

            function setLatLng(lat, lng, zoom) {
                var latlng = L.latLng(lat, lng);

                marker.setLatLng(latlng);
                map.setView(latlng, !zoom ? map.getZoom(): zoom , { animation: true });
                //map.panTo(latlng);

                $("#card-coordenadas #lat").val(lat);
                $("#card-coordenadas #lng").val(lng);
            }

            function updateLatLng() {
                var lat = $("#card-coordenadas #lat").val();
                var lng = $("#card-coordenadas #lng").val();

                if (lat && lng) {
                    var latlng = L.latLng(lat, lng);
                    marker.setLatLng(latlng);
                    map.setView(latlng,map.getZoom(), { animation: true });
                }
            }

            $(document).ready(function () {
                $("#card-coordenadas #lat").focusout(updateLatLng);
                $("#card-coordenadas #lng").focusout(updateLatLng);

                var container = $(".map-lat-lng");

                container.find('.input-lat-lng').keyup(function() {
                    if ($(this).val().length > 0) {
                        container.find(".btn-search").removeAttr("disabled");
                    } else {
                        container.find(".btn-search").attr("disabled", "disabled");
                    }
                });

                container.find('.btn-search').click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var address = container.find('.input-lat-lng').val();
                    // var city = $('#cidade_id option:selected').text();
                    // var state = $('#estado_id option:selected').text();

                    // if (city && $('#cidade_id').val() > 0) {
                    //     address = address + ","+city;
                    // }

                    // if (state && $('#estado_id').val() > 0) {
                    //     address = address + ","+state;
                    // }

                    if (address.length == 0) {
                        return;
                    }

                    var url = "https://nominatim.openstreetmap.org/search?q="+address+"&format=json";
                    $.getJSON(url, function(result) {
                        if (result.length > 0) {
                            var found = result[0];
                            setLatLng(found.lat, found.lon, 16);
                        } else {
                            alert("Não foi encontrado nenhum ponto com o endereço informado.")
                        }
                    });
                });
            });
        }, 500);
</script>
