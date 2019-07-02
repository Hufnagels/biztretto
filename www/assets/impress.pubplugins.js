(function ( document, window ) {
    'use strict';
  
  var stepids = [];

  // wait for impress.js to be initialized
  document.addEventListener("impress:init", function (event) {
    var steps = event.target.childNodes[0].children;//event.detail.steps;
    for (var i = 0; i < steps.length; i++)
    {
      stepids[i+1] = steps[i].id;
    }

  });
  
  //var progressbar = document.querySelector('div.progressbar div');
  //var progress = window.parent.document.querySelector('div.progress');
  var progress = document.querySelector('div.progress');
  
  if (null !== progress) {
    document.addEventListener("impress:starttransition", function (event) {
      updateProgressbar(event.detail.next.id);
    });
    
    document.addEventListener("impress:stepenter", function (event) {
      updateProgressbar(event.target.id);
    });
  }
    
  function updateProgressbar(slideId) {
    var slideNumber = stepids.indexOf(slideId);
    /*if (null !== progressbar) {
      progressbar.style.width = (100 / (stepids.length - 1) * (slideNumber)).toFixed(2) + '%';
    }
    */
    if(document.getElementById('menuList')){
        var list_items = document.getElementById('menuList').getElementsByTagName('li');
        for (var i=0, j=list_items.length; i<j; i++){
          var elm = list_items[i];
          elm.className = 'menu-item';
          if(i == slideNumber-1)
            elm.className += ' selected';
        }
    }
    if (null !== progress) {
      progress.innerHTML = slideNumber + '/' + (stepids.length-1);
    }
  }

})(document, window);