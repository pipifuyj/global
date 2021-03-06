/**
* require contextmenu.js setPointer.js
*/
var sduiHtmlSelectTableThead=$('sduiHtmlSelectTable').select("thead")[0];
//setColWidth
setColWidth(sduiHtmlSelectTableThead);
//when click the thead, order the data by the td.field
Event.observe(sduiHtmlSelectTableThead,"click",function(event){
		var td;
		if(td=Event.findElement(event,"td")){
			var sort=td.readAttribute("field");
			var dir=sduiHtmlSelectTableOptions.order[0][2];
			dir=(dir=="ASC")?"DESC":"ASC";
			location=sdui.toUrl()+"&order[0][0]="+sort+"&order[0][1]="+dir;
		}
});
//set the td.style.backgroundImage by the order
(function(){
	var sort=sduiHtmlSelectTableOptions.order[0][1];
	var dir=sduiHtmlSelectTableOptions.order[0][2];
	var td=sduiHtmlSelectTableThead.select('td[field='+sort+']')[0];
	td.setStyle({
			backgroundImage: "url(/global/image/blue/"+dir.toLowerCase()+".png)"
	});
})();
var sduiHtmlSelectTableTbody=$('sduiHtmlSelectTable').select("tbody")[0];
//setPointer
setPointer(sduiHtmlSelectTableTbody);
//set contextMenu of tbody.tr
if(!sduiHtmlSelectTableOptions.contextMenu)sduiHtmlSelectTableOptions.contextMenu=[
	['Update',sdui.toUrl({id:""},"Update")],
	['Delete',sdui.toUrl({id:""},"Delete")],
	['Insert',sdui.toUrl({id:""},"Insert")]
];
var sduiHtmlSelectTableTrs=sduiHtmlSelectTableTbody.select("tr");
if(sduiHtmlSelectTableOptions.contextMenu.length)sduiHtmlSelectTableTrs.each(function(tr){
	var id=tr.readAttribute('sduiID');
	var menuArray=[];
	sduiHtmlSelectTableOptions.contextMenu.each(function(menu,key){
		menuArray.push([menu[0],menu[1]+id]);
	});
	new contextMenu(tr,menuArray);
	Event.observe(tr,"dblclick",function(){
		location.href=sduiHtmlSelectTableOptions.contextMenu[0][1]+id;
	});
});
//search and paging Toolbar
var sduiHtmlSelectTableToolbar=$('sduiHtmlSelectTable').select("tfoot td")[0];
Element.update(sduiHtmlSelectTableToolbar,function(){
	var html="Select Where <select>";
	sduiHtmlSelectTableOptions.field.each(function(item){
		var field=item[1];
		var text=sdui.columns[0][field]['comment']||field;
		html+="<option value="+field+">"+text+"</option>";
	});
	html+="</select> like <input type=text /> <a href='#'>-Go-></a>";
		var page=sduiHtmlSelectTableOptions.start/sduiHtmlSelectTableOptions.limit+1;
		var pages=Math.ceil(sduiHtmlSelectTableOptions.count/sduiHtmlSelectTableOptions.limit);
		html+=" Page: "+page+"("+pages+") ";
		var i,ii,t;
		for(i=Math.max(1,page-5),ii=page;i<ii;i++){
			html+="<a href="
			+sdui.toUrl({start: sduiHtmlSelectTableOptions.limit*(i-1)})
			+">"+i+"</a> ";
		}
		html+=page;
		for(i=page+1,ii=Math.min(pages,page+5);i<=ii;i++){
			html+=" <a href="
			+sdui.toUrl({start: sduiHtmlSelectTableOptions.limit*(i-1)})
			+">"+i+"</a>";
		}
		return html;
}());
sduiHtmlSelectTableToolbar.Field=sduiHtmlSelectTableToolbar.select("select")[0];
sduiHtmlSelectTableToolbar.Keyword=sduiHtmlSelectTableToolbar.select("input")[0];
sduiHtmlSelectTableToolbar.Go=sduiHtmlSelectTableToolbar.select("a")[0];
Event.observe(sduiHtmlSelectTableToolbar.Go,"click",function(event){
		location.href=sdui.toUrl({"filter[0][0]":sduiHtmlSelectTableToolbar.Field.value,"filter[0][1]":"like","filter[0][2]":"%%"+sduiHtmlSelectTableToolbar.Keyword.value+"%%",start:0});
	Event.stop(event);
});
if(sduiHtmlSelectTableOptions.filter){
	sduiHtmlSelectTableToolbar.Field.value=sduiHtmlSelectTableOptions.filter[0][0];
	sduiHtmlSelectTableToolbar.Keyword.value=sduiHtmlSelectTableOptions.filter[0][2].replace(/%/g,"");
}
//fieldMap
$H(sduiHtmlSelectTableOptions.fieldMap).each(function(item){
		var field=item.key;
		var map=item.value;
		var index=-1;
		sduiHtmlSelectTableOptions.field.each(function(item,_index){
				if(item[1]==field)index=_index;
		});
		if(index<0)return;
		sduiHtmlSelectTableTrs.each(function(tr){
				var td=tr.select("td")[index];
				var a=[];
				td.innerHTML.split(",").each(function(name){
					a.push(map[name]);
				});
				td.update(a.join(","));
		});
});