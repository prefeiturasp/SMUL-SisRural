@component('mail::message')
    <p>Olá, <br><br>Existe uma aplicação do formulário {{ $checklist_name }} aguardando revisão.</p> <br> <p>Clique <a href="{{ $link }}">aqui</a> para acessar o SisRural e ver detalhes.</p>
@endcomponent
