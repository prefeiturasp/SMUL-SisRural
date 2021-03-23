<div class="c-loading">
    <div class="spinner-border text-light" role="status"></div>
</div>

@push('after-styles')
    <style>
        .c-loading {
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
    </style>
@endpush
