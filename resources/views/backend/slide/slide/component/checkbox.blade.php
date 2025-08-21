<div class="setting-item">
    <div class="uk-flex uk-flex-middle">
        <span class="setting-text">{!! $label !!}</span>
        <div class="setting-value">
            <input type="hidden" name="setting[{{ $key }}]" value="0">
            @php
                $value = old("setting.$key", $slide->setting[$key] ?? null);
                if ($value === null || $value === '') {
                    $value = 'accept';
                }
            @endphp
            <input type="checkbox"
                   name="setting[{{ $key }}]"
                   value="accept"
                   @if($value === 'accept') checked @endif>
        </div>
    </div>
</div>
