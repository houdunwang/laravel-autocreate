<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-md-9">
        <hd-datepicker name="{column['name']}" id="{column['name']}" value="{{ ${SMODEL}['{column['name']}']??old('{column['name']}') }}"></hd-datepicker>
        <br>
        @if ($errors->has('{column['name']}'))
            <span class="text-danger">
                <strong>{{ $errors->first('{column['name']}') }}</strong>
            </span>
        @endif
    </div>
</div>
