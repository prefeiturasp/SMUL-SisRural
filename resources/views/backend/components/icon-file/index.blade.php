
{{-- doc,docx,pdf,xls,xlsx,png,jpg,jpeg,gif,txt,kml,shp --}}
@php
    $class = '';
    $showExt = false;

    if ($ext == 'doc' || $ext == 'docx' || $ext == 'txt') {
        $class = 'far fa-file-word';
    } else if ($ext == 'pdf') {
        $class = 'far fa-file-pdf';
    } else if ($ext == 'xls' || $ext == 'xlsx') {
        $class = 'far fa-file-excel';
    } else if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') {
        $class = 'far fa-file-image';
    } else {
        $class = 'far fa-file';
        $showExt = true;
    }

@endphp

<i class="fa-file-ext {{$class}}">
    @if ($showExt)
        <span>{{$ext}}</span>
    @endif
</i>
