@extends('backend.layouts.app-template')

@section('body')



@include('backend.includes.sidebar')


    <div class="c-wrapper">



        @include('backend.includes.header')

        <div id="main-content" class="app-body c-body">
            <main class="c-main">
                @include('includes.partials.read-only')
                @include('includes.partials.logged-in-as')

                <div class="container-fluid">
                    <div class="animated fadeIn">
                        <div class="content-header">
                            @yield('page-header')
                        </div><!--content-header-->

                        @include('includes.partials.messages')
                        @yield('content')
                    </div><!--animated-->
                </div><!--container-fluid-->
            </main><!--main-->

            @include('backend.includes.aside')
        </div><!--app-body-->

        @include('backend.includes.footer')
    </div>


@endsection
