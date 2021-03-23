@extends('frontend.layouts.app')

@section('title', app_name())

    @push('after-styles')
        <script>
            window.location = "{{ route('admin.dashboard') }}";

        </script>
    @endpush
