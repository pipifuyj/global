Ext.BLANK_IMAGE_URL="http://global.googlecode.com/svn/trunk/extjs/image/default/s.gif";
Ext.IMAGE_URL=function(sGif){
	return Ext.BLANK_IMAGE_URL.replace("s.gif",sGif);
};
Ext.override(Ext.Container,{
	removeAll: function(autoDestroy){
		this.initItems();
		var rem=[],items=[];
		this.items.each(function(i){
			rem.push(i);
		});
		Ext.each(rem,function(item){
			this.remove(item,autoDestroy);
			if(item.ownerCt!==this)items.push(item);
		},this);
		return items;
	}
});
Ext.override(Ext.tree.TreeNode,{
/*	eachDescendant: function(f,scope){
		var i;
		for(i=0;i<this.childNodes.length;i++){
			if(f.call(scope,this.childNodes[i])){
				this.childNodes[i].eachDescendant(f,scope);
			}else{
				break;
			}
		}
	},
*/	findDescendantBy: function(f,scope){
		var node=null;
		this.eachChild(function(n){
			if(f.call(this,n)){
				node=n;
				return false;
			}else if(n.childNodes){
				if(n=n.findDescendantBy(f,scope)){
					node=n;
					return false;
				}else{
					return true;
				}
			}
		},scope);
		return node;
	},
	findDescendant: function(attribute,value){
		return this.findDescendantBy(function(node){
				return node.attributes[attribute]==value;
		});
	}
});
Ext.override(Ext.data.Record,{
	hasChanges: function(){
		var m=this.modified;
		for(var n in m)if(m.hasOwnProperty(n))return true;
		return false;
	}
});
