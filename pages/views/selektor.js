function removeOptions( selectBox )
{
    var i;
    for( i=selectBox.options.length-1; i>=0; i-- ) {
        selectBox.remove( i );
    }
}

function refreshSelect( index, selectBox ) {
    removeOptions( selectBox );

    var rsOptions = new Object;
    for( var i in rsFields ) {
        rsOptions[i] = rsFields[i];
    }
    for( var i in rsSelected ) {
        if( i == index )
            continue;

        delete rsOptions[ rsSelected[i] ];
    }

    // todo: sorted insertion...

    for( var i in rsOptions ) {
        selectBox.options[ selectBox.length ] = new Option( rsOptions[i], i, false, true );
    }
    if( rsSelected.hasOwnProperty( index ) ) {
        selectBox.value = rsSelected[ index ];
    }
    else
        selectBox.value = 'unused';
}

function fillKeyFields( ) {

    // todo: sorted insertion

    selectBox = document.getElementById( "keyfield" );
    removeOptions( selectBox );
    for( var i in rsSelected ) {
    	if (rsSelected[ i ] !== 'filename' && rsSelected[ i ] in rsNoFields)
            continue; // selecting an invalid key field that is not mapable to rsFields

 		var value = rsSelected[ i ];
 		var text = rsFields[ value ];
        selectBox.options[ selectBox.length ] = new Option( text, value, false, true );
    }

    selectBox.value = rsKeyField;
}

function refreshSelects( ) {
    for( var i = 0; i < js_data[0].length; i++ ) {
        var selectBox = getSelectBoxByCol( i+1 );
        refreshSelect( i, selectBox );
    }

    fillKeyFields();
}



function rsFieldSelected( col ) {
    var selectBox = getSelectBoxByCol( col );
    var index = col-1;

    // remember the value of that selectbox
    if( selectBox.value == 'unused' ) {
        if( rsSelected.hasOwnProperty( index ) ) {
            delete rsSelected[ index ];
        }
    }
    else {
        rsSelected[ index ] = selectBox.value;
    }

    refreshSelects( );
}

function rsKeyFieldSelected() {
    var selectBox = document.getElementById( "keyfield" );
    rsKeyField = selectBox.value;
}



function getSelectBoxByCol( col ) {
    var selectName = col + 'Select';
    return document.getElementById( selectName );
}

refreshSelects();
