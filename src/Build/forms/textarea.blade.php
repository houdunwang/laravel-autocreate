<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-md-9">
        <textarea id="{column['name']}" name="{column['name']}" rows="3" class="form-control">{{ ${SMODEL}['{column['name']}']??old('{column['name']}') }}</textarea>
    </div>
</div>
