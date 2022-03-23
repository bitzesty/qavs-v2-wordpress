jQuery( document ).ready( function( $ ) {
    $( '#npatt-csv-action' ).click( getCsv );

    function getCsv() {
        var button = this;
        $.ajax({
            'url': np_alt_tools.endpoint,
            'type': 'POST',
            'data': {
                'action': 'getCsv',
                'np_alt_tools_nonce': np_alt_tools.nonce
            },
            'beforeSend': function() {
                $( button ).html( 'Please wait... Loading' );
            }
        }).done( function( csvStr )  {
            $( button ).html( 'Download Alt Tag CSV' );
            download( 
                'data:Application/octet-stream,' + encodeURIComponent(
                    csvStr.replace(/\\r\\n/g, '\r\n').replace(/\\\/\\\//g, '/') )
                )
        });
    }

    function download( url ) {
        var a = document.createElement('a');
        a.download = createCSVName();
        a.href = url;
        a.click();
    }

    function createCSVName() {
        var d = new Date();
        return ( window.location.hostname.replace( /\./g, '-' ) + '-alt-tags-' 
                + d.getFullYear() + '-' + ( d.getMonth() + 1 ).toString().padStart( 2, 0 )
                + '-' + d.getDate().toString().padStart( 2, 0 ) + '.csv' );
    }
});
