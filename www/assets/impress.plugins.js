(function ( document, window ) {
    'use strict';
  
  var stepids = [];

		
  // wait for impress.js to be initialized
  document.addEventListener("impress:init", function (event) {
    var steps = event.target.childNodes[0].children;//event.detail.steps;
	
	var list_item = window.parent.document.getElementById('menuList');
    //list_item.innerHTML = '';
    for (var i = 0; i < steps.length; i++)
    {
      stepids[i+1] = steps[i].id;
	  if(list_item) {
	  var elm = document.createElement('li');
      elm.className = 'menu-item';
	  //var anch = document.createElement('a');
	  //anch.setAttribute('href','#');
	  elm.innerHTML = '<a href="#">'+steps[i].id+'</a>';
	  //elm.appenChild(anch);
	  list_item.appendChild(elm);
	  }
    }
	
	//addEventListenerByClass('menu-item', 'click', postMessageSend); 


  });
  
  //var progressbar = document.querySelector('div.progressbar div');
  //parent.document.body
  var progress = window.parent.document.querySelector('div.progress');
  
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
    if(window.parent.document.getElementById('menuList')){
        var list_items = window.parent.document.getElementById('menuList').getElementsByTagName('li');
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