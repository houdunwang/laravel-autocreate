<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-lg-9">
        <hd-image name="{column['name']}" id="{column['name']}" image-url="{!! ${SMODEL}['{column['name']}'] !!}"></hd-image>
    </div>
</div>
