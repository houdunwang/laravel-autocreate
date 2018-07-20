<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-md-9">
        <textarea id="{column['name']}" name="{column['name']}" rows="3"
                  class="form-control form-control{{ $errors->has('{column['name']}') ? ' is-invalid' : '' }}">
            {{ ${SMODEL}['{column['name']}']??old('{column['name']}') }}</textarea>
        @if ($errors->has('{column['name']}'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('{column['name']}') }}</strong>
            </span>
        @endif
    </div>
</div>
