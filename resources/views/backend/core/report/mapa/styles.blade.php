<style>
    .map-lat-lng {
        position: relative;
        margin:20px 0px;
    }
    .map-lat-lng #map-content {
        height:500px;
    }
    .map-lat-lng .loading {
        position: absolute;
        z-index:5000;

        top:0px; left:0px;
        width:100%; height:100%;

        background-color:rgba(0,0,0,.3);

        display: flex;
        justify-content: center;
        align-items: center;

        visibility:hidden;
        opacity:0;
        pointer-events: none;

        transition:all .3s;
    }
    .map-lat-lng.loading .loading {
        visibility:visible;
        opacity:1;
    }

    /* Popup do Marker / Infowindow*/
    .markerPopup .leaflet-popup-tip,
    .markerPopup .leaflet-popup-content-wrapper {
        overflow: hidden;
        background-color:#FFF;
        padding:0px;
    }
    .markerPopup .leaflet-popup-content {
        margin:0px;
    }
    .markerPopup a.leaflet-popup-close-button {
        color:#FFF;
    }
    .markerPopup .content-text {
        background-color:#08885b;
        padding:20px 25px;
    }
    .markerPopup .text-1 {
        font-size:18px;
        color:#FFF;
    }
    .markerPopup .text-2 {
        font-size:12px;
        color:#FFF;
        opacity:.7;
    }
    .markerPopup .text-3 {
        font-size:11px;
        color:#FFF;
        opacity:.5;
    }
    .markerPopup .content-btn {
        background-color:#FFF;
        padding:20px;
    }
    .markerPopup .content-btn .btn {
        width:100%;
        color:#FFF;
        background-color:#77D662;
        border-color:#77D662;
    }
</style>
