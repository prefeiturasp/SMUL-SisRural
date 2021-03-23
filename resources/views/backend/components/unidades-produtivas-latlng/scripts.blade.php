<style>
    .map-lat-lng {
        width:100%;
    }

    .map-lat-lng #map-content {
        width:100%;
        height:300px;
    }
</style>

<script>
    try {
        var unidadesProdutivas = JSON.parse('{!!json_encode($produtor->unidadesProdutivas->toArray())!!}');
        if (unidadesProdutivas.length > 0 ){
            var map = L.map('map-content').setView([unidadesProdutivas[0].lat, unidadesProdutivas[0].lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var markerArray = new Array();
            unidadesProdutivas.map(function (v) {
                var marker = L.marker([v.lat, v.lng], { title: v.nome, draggable:false }).addTo(map);
                markerArray.push(marker);
            });

            var group = new L.featureGroup(markerArray);
            map.fitBounds(group.getBounds());
        }
    } catch(e) {

    }
</script>
