/*

Custom digg widget
Author: Christopher Cook
Green Options Media
http://greenoptions.com/

*/

function digg_widget(settings) {
	if (typeof(window['jQuery']) != 'function') return;
	var $j=jQuery.noConflict();
	$j.ajaxSetup({ cache: true });
	
	var s = new function(){
		this.id = 'digg-widget-container';
		this.width = '300';
		this.height = '350';
		this.count = 10;
		this.endPoint = 'stories/popular';
		this.description = 0;
		this.domain = '';
		this.container = '';
		this.linkColor = '';
	};
	$j.extend(s, settings);
	if (!window.digg_widget_settings) window.digg_widget_settings = [];
	window.digg_widget_settings.push(s);
	window.digg_ran = window.digg_ran ? true : false;
	
	var i = digg_widget_settings.length - 1;
	var cb = 'digg_widget_cb_'+i;
	window[cb] = function(obj){ digg_widget_callback(obj, i); };
	
	var url = 'http://services.digg.com/' + s.endPoint + '?type=javascript&callback='+cb+'&appkey=http%3A%2F%2Fgreenoptions.com%2F' +
		'&count=' + s.count + ((s.domain)?'&domain='+s.domain:'');

	var html = '<div id="'+s.id+'" class="digg-widget digg-widget-theme1"'+ (s.width?' style="width:'+s.width+'px"':'') +'>'+
		'<script type="text/javascript" src="'+url+'"></scr'+'ipt>'+
		'<ul '+ (s.height?'style="height:'+s.height+'px" ':'') +'class="'+(parseInt(s.description)?'':'no-digg-description')+'"></ul></div>';
	
	if (!digg_ran) {
		$j('head').append('<link rel="stylesheet" type="text/css" media="all" href="http://digg.com/css/widget.css" />');
		window.digg_ran = true;
	}

	if (s.container) {
		$j(s.container).append(html);
	} else {
		document.write(html);
	}

}

function digg_widget_callback(obj,idx) {
	var s = digg_widget_settings[idx];
	var $j=jQuery.noConflict();
    $j('#'+s.id+' ul').html('');
    if(!obj) {
        $j('#'+s.id+' ul').html('We were unable to retrieve matching stories from Digg. Please refresh the page to try again.');
    }
    if(!obj.stories || obj.stories.length == 0) {
        $j('#'+s.id+' ul').html('Currently, there are no recent stories of this type.');
    }
    if (obj.stories) {
        for (var i = 0 ; i < obj.stories.length ; i++) {
        	story = obj.stories[i];
   	        if(story.diggs > 10000) {
       	        story.diggs = Math.floor(story.diggs/1000)+'K+';
           	}
            var e = '<li><a href="'+story.href+'?OTC-go" class="digg-count" target="_blank">'+story.diggs+' <span>diggs</span></a>'+
   	        	'<h3><a href="'+story.href+'"'+(s.linkColor?' style="color:'+s.linkColor+'"':'')+' target="_blank">'+story.title+'</a></h3>'+
       	    	(parseInt(s.description)?'':'<p>'+story.description+'</p>')+'</li>';
   	        $j('#'+s.id+' ul').append(e);
        }                 
    }
}

function digg_related(settings) {
	if (typeof(window['jQuery']) != 'function') return;
	var $j=jQuery.noConflict();
	$j.ajaxSetup({ cache: true });
	
	var s = new function(){
		this.id = 'digg-related-container';
		this.count = 3;
		this.endPoint = 'stories/popular';
	};
	$j.extend(s, settings);
	if (!window.digg_related_settings) window.digg_related_settings = [];
	window.digg_related_settings.push(s);
	
	var i = digg_related_settings.length - 1;
	var cb = 'digg_related_cb_'+i;
	window[cb] = function(obj){ digg_related_callback(obj, i); };
	
	var url = 'http://services.digg.com/' + s.endPoint + '?type=javascript&callback='+cb+'&appkey=http%3A%2F%2Fgreenoptions.com%2F' +
		'&count=' + s.count + ((s.domain)?'&domain='+s.domain:'');

	var html = '<div class="digg-related" style="display:none;"><div class="digg-related-prefix">You might also Digg:</div>'+ 
		'<div id="'+s.id+'" class="digg-related-links"'+ (s.width?' style="width:'+s.width+'px"':'') +'>'+
		'<script type="text/javascript" src="'+url+'"></scr'+'ipt>'+
		'<ul '+ (s.height?'style="height:'+s.height+'px" ':'') + '></ul></div><div class="clear"></div></div>';

	if (s.container) {
		$j(s.container).append(html);
	} else {
		document.write(html);
	}

}

function digg_related_callback(obj,idx) {
	s = digg_related_settings[idx];
	var $j=jQuery.noConflict();
    $j('#'+s.id+' ul').html('');
    if(!obj) {
        $j('#'+s.id+' ul').html('We were unable to retrieve matching stories from Digg. Please refresh the page to try again.');
    }
    if(!obj.stories || obj.stories.length == 0) {
        $j('#'+s.id+' ul').html('Currently, there are no recent stories of this type.');
    }
    var show = false;
    if (obj.stories) {
        for (var i = 0 ; i < obj.stories.length ; i++) {
        	story = obj.stories[i];
        	if (story.link == document.location)
        		continue;
   	        if(story.diggs > 10000) {
       	        story.diggs = Math.floor(story.diggs/1000)+'K+';
           	}
            var e = '<li><h3><a href="'+story.link+'">'+story.title+'</a></h3>'+
            	'<div class="digg-button"><a class="digg-count" href="http://digg.com/submit?phase=2&url='+escape(story.link)+'&title='+escape(story.title.replace(/\s+/g, "+"))+'" target="_blank">'+story.diggs+' digg'+(story.diggs>1?'s':'')+'</a></div></li>';
   	        $j('#'+s.id+' ul').append(e);
   	        show = true;
        }                 
    }
    if (show)
   		$j(".digg-related").show();
}