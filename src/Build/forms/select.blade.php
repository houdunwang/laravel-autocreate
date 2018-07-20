<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-md-9">
        <select id="{column['name']}" name="{column['name']}" class="form-control form-control-xs">
            {FORM_HTML}
        </select>
        <br>
        @if ($errors->has('{column['name']}'))
            <span class="text-danger">
                <strong>{{ $errors->first('{column['name']}') }}</strong>
            </span>
        @endif
    </div>
</div>
