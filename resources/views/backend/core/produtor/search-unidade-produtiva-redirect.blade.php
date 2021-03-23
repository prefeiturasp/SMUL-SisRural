@extends('backend.layouts.app-template', ['iframe'=>true])

@push('after-scripts')
    <script>
        window.top.location.href = base_url + "admin/produtor/sem_unidade";

    </script>
@endpush
