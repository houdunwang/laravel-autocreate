<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right"  style="padding-top:initial;">{column['title']}</label>
    <div class="col-12 col-md-9">
        <hd-simditor name="{column['name']}" url="/upload-simditor">{{ ${SMODEL}['{column['name']}']??old('{column['name']}') }}</hd-simditor>
        @if ($errors->has('{column['name']}'))
            <span class="text-danger">
                <strong>{{ $errors->first('{column['name']}') }}</strong>
            </span>
        @endif
    </div>
</div>
