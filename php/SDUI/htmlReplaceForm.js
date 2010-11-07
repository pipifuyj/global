/**
* require input.js
*/
setPointer("sduiHtmlReplaceForm",null,{mouse:false,click:false});
if(!Object.isArray(sduiHtmlReplaceFormValues)){
	sduiHtmlReplaceFormValues=new Hash(sduiHtmlReplaceFormValues);
	sduiHtmlReplaceFormValues.each(function(p){
			var input=$("sduiHtmlReplaceForm["+p.key+"]");
			if(Object.isString(p.value)){
				switch(p.value){
				case 'inputCalendar':
					inputCalendar(input);
					break;
				case 'inputEditor':
					inputEditor(input);
					break;
				case 'inputReadOnly':
					inputReadOnly(input);
					break;
				default:
					input.value=p.value;
					break;
				}
			}else if(Object.isArray(p.value)){
				if(Object.isArray(p.value[0])&&Object.isArray(p.value[1])){
					inputSelect(input,p.value[0],p.value[1],p.value[2]);
				}else{
					switch(p.value[0]){
					case 'inputCalendar':
						input.value=p.value[1];
						inputCalendar(input);
						break;
					case 'inputEditor':
						input.value=p.value[1];
						inputEditor(input);
						break;
					case 'inputReadOnly':
						input.value=p.value[1];
						inputReadOnly(input);
						break;
					default:
						inputSelect(input,p.value);
						break;
					}
				}
			}
	});
}
sdui.hiddenColumnNames=[];
sdui.hiddenColumns.each(function(column,index){
	var name=column.name;
	sdui.hiddenColumnNames.push(name);
});
sduiHtmlReplaceFormTools={
	Toggle: function(event){
		$$("#sduiHtmlReplaceForm li").each(function(li,index){
			var column=li.getAttribute('data-column');
			if(sdui.hiddenColumnNames.indexOf(column)>-1)return;
			var input=$("sduiHtmlReplaceForm["+column+"]");
			if(input.value)return;
			li.style.display=li.style.display=='none'?'':'none';
		});
	}
}
if(sdui.action='Update')sduiHtmlReplaceFormTools.Toggle();
$$("#sduiHtmlReplaceForm legend input").each(function(input,index){
	Event.observe(input,'click',sduiHtmlReplaceFormTools[input.value]);
});
(function(){
	$$("textarea").each(function(textarea,key){
			inputEditor(textarea,true);
			inputFullScreen(textarea);
	});
})();
