<div class="card-add-view card">
    <div class="card-header d-flex align-items-center">
        <div class="square">
            <div class="{{$icon}}"></div>
        </div>

        <div class="title ml-2">
            {{$title}}
        </div>
    </div>

    <div class="card-body {{@$noPadding ? 'no-padding' : ''}}">
        @can(@$permissionView)
            @if (@$linkView)
                <a class="d-block btn btn-outline-primary" href="{{$linkView}}" target="_self" alt="{{$labelView}}">
                    {{$labelView}}

                    @if (@$total)
                        <span>({{$total}})</span>
                    @endif
                </a>
            @endif
        @endcan

        @can(@$permissionAdd)
            @if (@$linkAdd)
                <a class="d-block btn btn-primary mt-3" href="{{$linkAdd}}" target="_self" alt="{{$labelAdd}}">
                    <i class="c-icon cil-plus"></i>
                    <span class="ml-2">{{$labelAdd}}</span>
                </a>
            @endif

            @if (@$linkAdd2)
                <a class="d-block btn btn-primary mt-3" href="{{$linkAdd2}}" target="_self" alt="{{$labelAdd2}}">
                    <i class="c-icon cil-plus"></i>
                    <span class="ml-2">{{$labelAdd2}}</span>
                </a>
            @endif
        @endcan

        {{ @$body }}
    </div>
</div>
