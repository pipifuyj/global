Ext.grid.CellCheckboxSelectionModel = Ext.extend(Ext.grid.CellSelectionModel, {
    header: '<div class="x-grid3-hd-checker">&#160;</div>',
    width: 20,
    sortable: false,
    menuDisabled: true,
    fixed: true,
    dataIndex: '',
    id: 'checker',
	singleSelect: false,

    constructor: function(){
        Ext.grid.CellCheckboxSelectionModel.superclass.constructor.apply(this, arguments);
		this.selections = new Ext.util.MixedCollection(false, function(o){
				return o.id;
		});
		this.last = false;
		this.lastActive = false;
		this.addEvents("rowselect","rowdeselect");
    },
	
	isSelected: function(index){
		var r = typeof index == "number" ? this.grid.store.getAt(index) : index;
		return (r && this.selections.key(r.id) ? true : false);
	},
	
    getSelected : function(){
        return this.selections.itemAt(0);
    },
	
    getSelections : function(){
        return [].concat(this.selections.items);
    },
	
	deselectRow: function(index,preventViewNotify){
        if(this.locked) return;
        if(this.last == index) this.last = false;
        if(this.lastActive == index) this.lastActive = false;
        var r = this.grid.store.getAt(index);
        if(r){
            this.selections.remove(r);
            if(!preventViewNotify){
                this.grid.getView().onRowDeselect(index);
            }
            this.fireEvent("rowdeselect", this, index, r);
            this.fireEvent("selectionchange", this);
        }
	},
	
	selectRow: function(index,keepExisting,preventViewNotify){
        if(this.locked || (index < 0 || index >= this.grid.store.getCount()) || this.isSelected(index)) return;
        var r = this.grid.store.getAt(index);
        if(r){
            if(!keepExisting || this.singleSelect){
                this.clearCheckboxSelections();
            }
            this.selections.add(r);
            this.last = this.lastActive = index;
            if(!preventViewNotify){
                this.grid.getView().onRowSelect(index);
            }
            this.fireEvent("rowselect", this, index, r);
            this.fireEvent("selectionchange", this);
        }
	},
	
	clearCheckboxSelections: function(fast){
        if(this.locked) return;
        if(fast !== true){
            var ds = this.grid.store;
            var s = this.selections;
            s.each(function(r){
                this.deselectRow(ds.indexOfId(r.id));
            }, this);
            s.clear();
        }else{
            this.selections.clear();
        }
        this.last = false;
	},
	
	selectAll: function(){
        if(this.locked) return;
        this.selections.clear();
        for(var i = 0, len = this.grid.store.getCount(); i < len; i++){
            this.selectRow(i, true);
        }
	},

    // private
    initEvents: function(){
        Ext.grid.CellCheckboxSelectionModel.superclass.initEvents.call(this);
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
            Ext.fly(view.innerHd).on('mousedown', this.onHdMouseDown, this);
        }, this);
    },

    // private
    onMouseDown: function(e, t){
        if(e.button === 0 && t.className == 'x-grid3-row-checker'){ // Only fire if left-click
            var row = e.getTarget('.x-grid3-row');
            if(row){
                var index = row.rowIndex;
                if(this.isSelected(index)){
                    this.deselectRow(index);
                }else{
                    this.selectRow(index, true);
                }
            }
        }
    },

    // private
    onHdMouseDown: function(e, t){
        if(t.className == 'x-grid3-hd-checker'){
            e.stopEvent();
            var hd = Ext.fly(t.parentNode);
            var isChecked = hd.hasClass('x-grid3-hd-checker-on');
            if(isChecked){
                hd.removeClass('x-grid3-hd-checker-on');
                this.clearCheckboxSelections();
            }else{
                hd.addClass('x-grid3-hd-checker-on');
                this.selectAll();
            }
        }
    },

    // private
    renderer: function(v, p, record){
        return '<div class="x-grid3-row-checker">&#160;</div>';
    }
});
