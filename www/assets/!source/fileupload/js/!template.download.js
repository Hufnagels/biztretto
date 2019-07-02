{% for (var i=0, file; file=o.files[i]; i++) { %}
     <li class="template-upload fade span2" >
        {% if (file.error) { %}
            <td></td>
            <p class="name"><span>{%=file.name%}</span></p>
            <p class="size"><span>{%=o.formatFileSize(file.size)%}</span></p>
            <p class="error"><span class="label label-important">Error</span> {%=file.error%}</p>
        {% } else { %}
            <div class="thumbnail">{% if (file.thumbnail_url) { %}<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>{% } %}
              <p class="name"><span>File: {%=file.name%}</span></p>
              <p class="size"><span>Type: {%=file.type%}</span></p>
              <p class="size"><span>Uploaded: {%=o.formatFileSize(file.size)%}</span></p>
              <span class="delete"><button class="btn btn-light" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}><i class="icon-trash"></i><span>Delete</span></button>
              <input type="checkbox" name="delete" value="1"></span>
            </div>
        {% } %}
    </li>
{% } %}