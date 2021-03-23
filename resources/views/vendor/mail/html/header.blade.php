<tr>
    <td class="header">
        <a href="{{ $url }}" target="_blank" style="float:left; margin-left:28px;">
            <img src="{{ $url.'/img/email/logo.png' }}" />
        </a>

        <div style="float:right; margin-right:28px; margin-top:13px;">
            <?= \Carbon\Carbon::now()->format('d \d\e F \d\e Y')?>
        </div>
    </td>
</tr>
