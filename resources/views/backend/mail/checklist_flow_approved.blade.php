@component('mail::message')
    <p>Olá, <br><br>A aplicação do formulário {{ $checklist_name }} foi aprovada.</p> <br> <p>Clique <a href="{{ $link }}">aqui</a> para acessar o SisRural e ver detalhes.</p>
@endcomponent
