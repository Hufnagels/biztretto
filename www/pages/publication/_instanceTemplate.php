<script type="text/x-tmpl" id="tmpl-traininginstances">
    {% for (var i=0, slide; slide=o.result[i]; i++) { %}
    <li class="userElement span8 lightgreyB" data-id="{%=slide.id%}">
        <span class="colorBar"></span>
        <div class="trainingdata">
            <div class="span1 pull-left">
                <div class="badge level">{%=i+1%}</div>
            </div>
            <div class="parameters trainingdataRow pull-left">

                <form id="slideshow_{%=slide.id%}" action="" methode="post" class="form-horizontal">
                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 200px;vertical-align: top;">
                                <div class="control-group">
                                    <label class="control-label" for="<?= $formdata['id'][0]; ?>"><?=$formdata['label'][0];?></label>
                                    <div class="controls input-append date" data-date-format="yyyy-mm-dd" data-date="<?= date("Y-m-d", time()); ?>">
                                        <input type="text" name="<?= $formdata['id'][0]; ?>" placeholder="<?= $formdata['pholder'][0]; ?>" value="{%=slide.startDate%}" class="dateHU input-small"/><span class="add-on"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="<?= $formdata['id'][1]; ?>"><?=$formdata['label'][1];?></label>
                                    <div class="controls input-append date" data-date-format="yyyy-mm-dd" data-date="<?= date("Y-m-d", time()); ?>">
                                        <input type="text" name="<?= $formdata['id'][1]; ?>" placeholder="<?= $formdata['pholder'][1]; ?>" value="{%=slide.endDate%}" class="dateHU input-small"/><span class="add-on"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>
                              </td>
                              <td>
                                <div class="control-group">
                                    <label class="control-label" for="<?= $formdata['id'][2]; ?>"><?=$formdata['label'][2];?></label>
                                    <div class="">
                                      <span class="controls input-append bootstrap-timepicker">
                                        <input type="text" name="<?= $formdata['id'][2]; ?>" placeholder="<?= $formdata['pholder'][2]; ?>" value="{%=slide.timeout1%}" class="input-xxsmall timepicker"/><span class="add-on"><i class="icon-time"></i></span>
                                      </span>
                                      <span class="controls input-append bootstrap-timepicker">
                                        <input type="text" name="<?= $formdata['id'][3]; ?>" placeholder="<?= $formdata['pholder'][2]; ?>" value="{%=slide.timeout2%}" class="input-xxsmall timepicker"/><span class="add-on"><i class="icon-time"></i></span>
                                      </span><!---->
                                    </div>
                                </div>

                                <div class="control-group ">
                                    <label class="control-label" for="<?= $formdata['id'][4]; ?>"><?=$formdata['label'][4];?></label>
                                    <div class="">
                                      <span class="controls input-append bootstrap-timepicker">
                                        <input type="text" name="<?= $formdata['id'][4]; ?>" placeholder="<?= $formdata['pholder'][4]; ?>" value="{%=slide.wtimeout1%}" class="input-xxsmall timepicker"/><span class="add-on"><i  class="icon-time"></i></span>
                                      </span>
                                      <span class="controls input-append bootstrap-timepicker">
                                        <input type="text" name="<?= $formdata['id'][5]; ?>" placeholder="<?= $formdata['pholder'][5]; ?>" value="{%=slide.wtimeout2%}" class="input-xxsmall timepicker"/><span class="add-on"><i class="icon-time"></i></span>
                                      </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;vertical-align: top;">

                            </td>
                        </tr>
                    </table>

                </form>

            </div>
            <div class="users span1"><span class="droppedusers badge" data-traininggroup="{%=slide.group%}">{% if (slide.group =='' ) { %}Drop here{% } else { %}{%=slide.users %} user{% } %}</span></div>
            <div class="action">
                <span class="btn-dark removeInstance"><span class="icon-white icon-minus"></span></span>
            </div>
        </div>

    </li>
    {% } %}
</script>