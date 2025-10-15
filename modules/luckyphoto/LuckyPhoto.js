/* Copyright 2006 LuckyTeam.co.uk. To use this code on your own site, visit http://luckyteam.co.uk */

var luckyPhoto_ua = 'msie';
var W=navigator.userAgent.toLowerCase();
if(W.indexOf("opera")!=-1){luckyPhoto_ua='opera';}else if(W.indexOf("msie")!=-1){luckyPhoto_ua='msie';}  else if(W.indexOf("mozilla")!=-1){luckyPhoto_ua='gecko';}

function _el(id){ return document.getElementById(id); };

function luckyPhoto_addEventListener(obj, event, listener){
    if(luckyPhoto_ua == 'gecko' || luckyPhoto_ua == 'opera'){
        obj.addEventListener(event, listener, false);
    } else if (luckyPhoto_ua == 'msie') {
        obj.attachEvent("on"+event,listener);
    }
};

function luckyPhoto_removeEventListener(obj, event, listener){
    if(luckyPhoto_ua == 'gecko' || luckyPhoto_ua == 'opera'){
        obj.removeEventListener(event, listener, false);
    } else if (luckyPhoto_ua == 'msie') {
        obj.detachEvent("on"+event,listener);
    }
};

function luckyPhoto_createMethodReference(object, methodName) {
    var args = arguments;
        return function () {
        object[methodName].apply(object, arguments, "");
    };
};

//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.org
//
function lp_getPageScroll(){

    var yScroll;

    if (self.pageYOffset) {
        yScroll = self.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop){  // Explorer 6 Strict
        yScroll = document.documentElement.scrollTop;
    } else if (document.body) {// all other Explorers
        yScroll = document.body.scrollTop;
    }

    arrayPageScroll = new Array('',yScroll)
    return arrayPageScroll;
}

//
// getPageSize()
// Returns array with page width, height and window width, height
//
function lp_getPageSize(){

        var xScroll, yScroll, pageHeight, pageWidth;

        if (window.innerHeight && window.scrollMaxY) {
                xScroll = document.body.scrollWidth;
                yScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
                xScroll = document.body.scrollWidth;
                yScroll = document.body.scrollHeight;
        } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
                xScroll = document.body.offsetWidth;
                yScroll = document.body.offsetHeight;
        }

        var windowWidth, windowHeight;
        if (self.innerHeight) { // all except Explorer
                windowWidth = self.innerWidth;
                windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
                windowWidth = document.documentElement.clientWidth;
                windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
                windowWidth = document.body.clientWidth;
                windowHeight = document.body.clientHeight;
        }

        // for small pages with total height less then height of the viewport
        if(yScroll < windowHeight){
                pageHeight = windowHeight;
        } else {
                pageHeight = yScroll;
        }

        // for small pages with total width less then width of the viewport
        if(xScroll < windowWidth){
                pageWidth = windowWidth;
        } else {
                pageWidth = xScroll;
        }

        arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
        return arrayPageSize;
}


function luckyPhoto(photos, options){

    this.opacity = 0;
    this.popacity = 0;
    this.layer = null;
    this.img = null;
    this.preload_img = null;
    this.baseuri = '';
    this.closeImg = null;

    this.photos = photos;
    this.options = options;
    this.tip = null;

    this.safariOnLoadStarted = false;
    this.stopListener = luckyPhoto_createMethodReference(this, "stop");
};

/*
luckyPhoto.prototype.swapImage = function (e) {
    var i = e.currentTarget || e.srcElement || e;
    if(i.src.indexOf('close')!=-1){
        if(i.src.indexOf('_on')!=-1){
            i.src = this.baseuri + '/close.png';
        } else {
            i.src = this.baseuri + '/close_on.png';
        }
    }
}
*/

luckyPhoto.prototype.changeOpacity = function () {
    if(this.options['speed']>99){
        this.opacity = this.options['background_opacity'];
        this.popacity = 100;
    }
    if(this.opacity <= this.options['background_opacity']){
        this.layer.style.opacity = (this.opacity / 100);
        this.layer.style.MozOpacity = (this.opacity / 100);
        this.layer.style.KhtmlOpacity = (this.opacity / 100);
        this.layer.style.filter = "alpha(opacity=" + this.opacity + ")";
        this.opacity += 7;
    }

    if(this.popacity <= 100){
        this.img.style.opacity = (this.popacity / 100);
        this.img.style.MozOpacity = (this.popacity / 100);
        this.img.style.KhtmlOpacity = (this.popacity / 100);
        this.img.style.filter = "alpha(opacity=" + this.popacity + ")";
        this.popacity += 15;
        if(this.popacity > 100) this.popacity = 100;
    }

    if(this.opacity < this.options['background_opacity'] || this.popacity < 100){
        setTimeout(luckyPhoto_createMethodReference(this, "changeOpacity"), 200-(200*(this.options['speed']+1)/100));
    } else {
        luckyPhoto_addEventListener(this.img, "click", this.stopListener);
    }
}

luckyPhoto.prototype.showPhoto = function () {

    this.preload_img.style.visibility = 'hidden';
    this.preload_img.style.display = 'none';

    /*
    var h = 0;
    var windowHeight = 0;

    if (W.indexOf("safari") != -1) {
        windowHeight = window.innerHeight;
        h = window.pageYOffset;
    } else if (luckyPhoto_ua=='opera') {
        h = window.pageYOffset;
        windowHeight = document.body.clientHeight;
    } else {
        if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ){
            h = document.body.scrollTop;
        } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
            h = document.documentElement.scrollTop;
        }
		windowHeight = document.documentElement.clientHeight;
    }

    if(typeof h == 'undefined') h = 0;

    this.img.style.position = 'absolute';
    this.img.style.zIndex = 1002;
    this.img.style.border = this.options['image_border'];
    this.img.style.visibility = 'visible';
    this.img.style.display = '';
    this.img.style.left = (parseInt(this.layer.style.width)-parseInt(this.img.width))/2 + 'px';
    this.img.style.top = (parseInt(h)+(parseInt(windowHeight)-parseInt(this.img.height))/2) + 'px';
    */
    var szArr = lp_getPageSize();
    var scrArr = lp_getPageScroll();

    this.img.style.position = 'absolute';
    this.img.style.zIndex = 1002;
    this.img.style.border = this.options['image_border'];
    this.img.style.visibility = 'visible';
    this.img.style.display = '';
    this.img.style.left = (scrArr[0]+(szArr[2]-parseInt(this.img.width))/2) + 'px';
    this.img.style.top = (scrArr[1]+(szArr[3]-parseInt(this.img.height))/2) + 'px';
}

luckyPhoto.prototype.stop = function () {
    if(this.img != null){
	    this.img.style.visibility = 'hidden';
	    this.img.style.display = 'none';
	    luckyPhoto_removeEventListener(this.img, "click", this.stopListener);
    }
    if(this.layer != null){
	    document.body.removeChild(this.layer);
    }
    this.layer = null;
    this.img = null;
}

luckyPhoto.prototype.start = function (e) {

    var o = e.currentTarget || e.srcElement || e;
    if(!o.id){
	    o = o.parentNode;
    }
    this.opacity = 0;
    this.popacity = 0;

    var szArr = lp_getPageSize();
    var scrArr = lp_getPageScroll();

    if(_el(o.id+'_lp_big')){
        this.img = _el(o.id+'_lp_big');
        this.img.src = this.photos[o.id];
        this.img.style.visibility = 'hidden';
        this.img.style.display = 'none';
    } else {
        this.img = document.createElement('IMG');
        this.img.src = this.photos[o.id];
        this.img.id = o.id+'_lp_big';

        this.img.style.visibility = 'hidden';
        this.img.style.display = 'none';
        document.body.appendChild(this.img);
    }

    this.img.style.opacity = (this.popacity / 100);
    this.img.style.MozOpacity = (this.popacity / 100);
    this.img.style.KhtmlOpacity = (this.popacity / 100);
    this.img.style.filter = "alpha(opacity=" + this.popacity + ")";

    if(!this.options['preload']){
        this.preload_img.style.display = '';
        this.preload_img.style.visibility = 'visible';
        this.preload_img.style.position = 'absolute';
        this.preload_img.style.left = (scrArr[0]+(szArr[2]-parseInt(this.preload_img.width))/2) + 'px';
        this.preload_img.style.top = (scrArr[1]+(szArr[3]-parseInt(this.preload_img.height))/2) + 'px';
        this.preload_img.style.zIndex = 1001;
    }

    if(this.layer == null){
        this.layer = document.createElement("DIV");
        this.layer.style.zIndex = 1000;
        this.layer.style.position = "absolute";
        this.layer.style.left = '0px';
        this.layer.style.top = '0px';
        this.layer.style.background = this.options['background_color'];
        this.layer.id = 'luckyPhoto';
        this.layer.style.opacity = (this.opacity / 100);
        this.layer.style.MozOpacity = (this.opacity / 100);
        this.layer.style.KhtmlOpacity = (this.opacity / 100);
        this.layer.style.filter = "alpha(opacity=" + this.opacity + ")";
    }

    if(szArr[1]>szArr[3] && luckyPhoto_ua == 'gecko'){
	    szArr[0] -= 20;
    }

    this.layer.style.width = szArr[0]+'px';
    this.layer.style.height = szArr[1]+'px';

    if(luckyPhoto_ua == 'msie'){
        var f = document.createElement("IFRAME");
        f.style.left = '0px';
        f.style.top = '0px';
        f.style.position = 'absolute';
        f.style.filter='progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)';
        f.style.width = this.layer.style.width;
        f.style.height = this.layer.style.height;


        f.frameBorder = 0;
        this.layer.appendChild(f);
    }

    document.body.appendChild(this.layer);

    if(this.options['preload']){
        this.showPhoto();
    } else {
        this.preloadImage();
    }
    this.changeOpacity();
}

luckyPhoto.prototype.preloadImage = function () {

    if(W.indexOf("safari")!=-1){
        if(!this.safariOnLoadStarted){
            luckyPhoto_addEventListener(this.img, "load", luckyPhoto_createMethodReference(this, "preloadImage"));
            this.safariOnLoadStarted = true;
            return;
        }
    } else {
        if(!this.img.complete){
            setTimeout(luckyPhoto_createMethodReference(this, "preloadImage"), 100);
            return;
        }
    }

    this.safariOnLoadStarted = false;
    this.showPhoto();
}

luckyPhoto.prototype.subinit = function () {

    if(this.options['preload']){
        for(p in this.photos){
            if(W.indexOf("safari")!=-1){
                if(!this.safariOnLoadStarted){
                    luckyPhoto_addEventListener(_el(p+'_lp_big'), "load", luckyPhoto_createMethodReference(this, "subinit"));
                    this.safariOnLoadStarted = true;
                    return;
                }
            } else {
                if(!_el(p+'_lp_big').complete){
                    setTimeout(luckyPhoto_createMethodReference(this, "subinit"), 100);
                    return;
                }
            }
        }
    } else {
        this.preload_img = document.createElement('IMG');
        this.preload_img.src = this.options['preload_image'];
        this.preload_img.style.visibility = 'hidden';
        this.preload_img.style.display = 'none';
        document.body.appendChild(this.preload_img);
    }

    for(p in this.photos){
        _el(p).style.cursor = 'pointer';
		if(_el(p).tagName == 'DIV'){ //virtuemart module...
		  _el(p).alt = _el(p.replace(/^sc/, 'sim')).alt + ': Click to ZOOM';
		} else {
      	  if(_el(p).alt != ''){
            _el(p).alt = _el(p).alt + ': Click to ZOOM';
      	  } else {
            _el(p).alt = 'Click to ZOOM';
      	  }
		}
        _el(p).title = _el(p).alt;
        luckyPhoto_addEventListener(_el(p), "click", luckyPhoto_createMethodReference(this, "start"));
    }
}

luckyPhoto.prototype.preloadImages = function () {

    for(p in this.photos){
        var img = document.createElement('IMG');
        img.src = this.photos[p];
        img.id = p+'_lp_big';

        img.style.visibility = 'hidden';
        img.style.display = 'none';
        document.body.appendChild(img);
    }

    this.subinit();
}

luckyPhoto.prototype.init = function () {

    if(this.options['preload']){
        luckyPhoto_addEventListener(window, "load", luckyPhoto_createMethodReference(this, "preloadImages"));
    } else {
        luckyPhoto_addEventListener(window, "load", luckyPhoto_createMethodReference(this, "subinit"));
    }

};
