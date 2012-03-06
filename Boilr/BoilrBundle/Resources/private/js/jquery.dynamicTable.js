(function($, window, document, undefined) {

    var DynamicTable = function( oInit )
    {

        this.fnAddRow = function( )
        {
            var oSettings = _settingsForTable( this[DynamicTable.models.ext.iApiIndex] );
            var index = $('tbody tr', this).length;
            var text  = oSettings['sProto'].html();
            var row = text.replace(/\$\$name\$\$/g, index);

            $('tbody', this).append(row);

            if ( oSettings['fnAddCallback'] !== undefined ) {
                oSettings['fnAddCallback']();
            }
        };

        this.fnRemoveRow = function( oLink )
        {
            var oSettings = _settingsForTable( this[DynamicTable.models.ext.iApiIndex] );
            var iLen = $('tbody tr', this).length;
            var bDelete = false;

            if ( (iLen > 1) || ((iLen == 1) && (oSettings['bAllowNoRecords'])) ) {
                bDelete = true;
            }

            if (bDelete) {
                $(oLink).closest('tr').remove();
            }
        };

        function fnClickHandler( oLink )
        {
            if ($(oLink).hasClass("addrow")) {
                this.fnAddRow();
            } else if ($(oLink).hasClass("removerow")) {
                var oTable = $(oLink).closest('table').dynamicTable();
                oTable.fnRemoveRow( oLink );
            }
        };

        function _settingsForTable( nTable )
        {
            for ( var i=0 ; i<DynamicTable.settings.length ; i++ ) {
                if ( DynamicTable.settings[i].nTable === nTable ) {
                    return DynamicTable.settings[i];
                }
            }

            return null;
        }

        var _that = this;
        return this.each(function()
                {
                    var i=0, iLen;

                    // Sanity check: only tables are allowed
                    if ( this.nodeName.toLowerCase() != 'table' ) {
                        console.log("Attempted to initialise DynamicTables on a node which is not a table: "+this.nodeName );
                        return;
                    }

                    // Check if given table was already initialized
                    for ( i=0, iLen=DynamicTable.settings.length ; i<iLen ; i++ ) {
                        if ( DynamicTable.settings[i].nTable == this ) {
                            if ( oInit === undefined ) {
                                return DynamicTable.settings[i].oInstance;
                            }

                            console.log( "Cannot reinitialise DynamicTable.\n" );
                            return;
                        }
                    }

                    // Extends defaults settings and save for later uses
                    var oSettings = $.extend( true, {}, DynamicTable.oDefaults, {"nTable": this} );
                    oSettings = $.extend( oSettings, oInit );
                    DynamicTable.settings.push( oSettings );
                    oSettings.oInstance = (_that.length===1) ? _that : $(this).dynamicTable();

                    $(this).addClass( oSettings.sClass );

                    // If table is empty and "sAddIfEmpty" is true, add a new row
                    if ($('tbody tr', this).length === 0 && oSettings['bAddIfEmpty'] === true) {
                        this.fnAddRow();
                    }

                    $('tbody a', this).bind('click', function() {
                        fnClickHandler( this );
                    });
                });
    }

    DynamicTable.models = {};

    DynamicTable.models.ext = {
        "iApiIndex": 0
    };

    DynamicTable.settings = [ ];

    DynamicTable.version = "1.0.1";

    /**
     * Default values
     */
    DynamicTable.oDefaults = {
                'iMaxRows'        : -1,
                'bAddIfEmpty'     : true,
                'bAllowNoRecords' : false,
                'sProto'          : undefined,
                'fnAddCallback'   : undefined,
                'sClass'          : 'dynamicTable'
        };

    // jQuery aliases
    $.fn.DynamicTable = DynamicTable;
    $.fn.dynamicTable = DynamicTable;

}(jQuery, window, document, undefined));