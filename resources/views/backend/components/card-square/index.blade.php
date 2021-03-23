<a class="card-square card {{@$white ? 'card-square-white  bg-white' : 'bg-primary'}}" href="{{$link}}">
    <div class="card-body {{@$white ? 'text-primary' : 'text-white'}}">
        <div class="square">
            <div class="{{$icon}}"></div>
        </div>

        <div class="ml-4">
            @if (@$total)
                <h2 class="{{@$white ? 'font-weight-bold' : ''}}">{{$total}}</h2>
            @endif

            <h5>{{$title}}</h5>
        </div>
    </div>
</a>
