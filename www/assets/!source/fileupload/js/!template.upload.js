{% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-upload fade span2" >
      <div class="thumbnail ">
        <div class="preview"><span class="fade"></span></div>
        <p class="name"><span>File: {%=file.name%}</span></p>
        <p class="size"><span>Type: {%=file.type%}</span></p>
        {% if (file.error) { %}
            <p class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</p>
        {% } else if (o.files.valid && !i) { %}
            <p>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </p>
            <span class="start">{% if (!o.options.autoUpload) { %}<button class="btn btn-dark"><i class="icon-upload icon-white"></i><span>Start</span></button>{% } %}</span>
            <span class="cancel">{% if (!i) { %}
            <button class="btn btn-light">
                <i class="icon-ban-circle"></i>
                <span>Cancel</span>
            </button>
        {% } %}</span>
        {% } else { %}
            <p colspan="2"></p>
        {% } %}
        
      </div>
    </li>
{% } %}