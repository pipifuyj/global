function AutoScroll(element, timeout){
  this.Timeout=timeout;
  this.stopscroll=false;
  this.element=element;
  this.element.onmouseover=this.getFunction("MouseOver");
  this.element.onmouseout=this.getFunction("MouseOut");
}
AutoScroll.prototype={
  start: function(method){
    setInterval(this.getFunction(method), this.Timeout);
  },
  MouseOver: function(){
    this.stopscroll=true;
  },
  MouseOut: function(){
    this.stopscroll=false;
  },
  SmoothScroll: function(){
	if(!this.smoothed){
		this.smoothed=true;
		this.element.style.overflow="hidden";
		this.element.innerHTML+=this.element.innerHTML;
		this.scrollMax=this.element.scrollHeight/2;
	}
    if(this.stopscroll)return;
    this.element.scrollTop++;
    if(this.element.scrollTop>=this.scrollMax)this.element.scrollTop=0;
  },
  getFunction: function(method, param){
	var self=this;
    return function(){
      self[method](param);
    }
  }
}