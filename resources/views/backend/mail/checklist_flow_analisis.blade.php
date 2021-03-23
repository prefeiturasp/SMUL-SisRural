@component('mail::message')
    <p>Olá, <br><br>O formulário {{ $checklist_name }} está disponível para revisão.</p> <br> <p>Clique <a href="{{ $link }}">aqui</a> para acessar o SisRural e ver detalhes.</p>
@endcomponent
