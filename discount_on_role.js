jQuery(document).ready(function($){
    $('select#role').on('change', function() {
        if( ( this.value ) == 'userdiscount' ) {
            $('tr#discount-on-role-row').show();
        } else {
            $('tr#discount-on-role-row').hide();
        }
    });

    field = $('tr#discount-on-role-row').remove();
    field.insertAfter('tr.user-role-wrap');
});