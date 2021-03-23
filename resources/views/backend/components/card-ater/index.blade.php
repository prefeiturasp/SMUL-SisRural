<div id="{{@$id}}" class="card card-ater {{@$class}}">
    @if (@$title)
        <div class="card-header">
            <div class="row">
                <div class="{{@$headerRight ? 'col-sm-5': 'col-sm-12'}}">
                    <{{@$titleTag ?? 'h2'}} class="card-title mb-0 mt-1 h4">
                        {{@$title}}
                    </{{@$titleTag  ?? 'h2'}}>
                </div>

                @if (@$headerRight)
                    <div class="col-sm-7 pull-right">
                        {{ @$headerRight }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if (@$body)
        <div class="card-body">
            {{ $body }}
        </div>
    @endif

    @if(@$footer)
        <div class="card-footer button-group">
            {{ $footer }}
        </div>
    @endif
</div>
