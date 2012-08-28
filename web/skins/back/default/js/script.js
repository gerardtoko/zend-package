/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function() {

    $('.dell_button').live("click", function() {
        $('.table-form').submit();
    });
    
    $('.search-button').live("click", function(){
        var val_input = $('.search-val').val();
        var val_form = $('.form-search').attr('action');
        var url = val_form + val_input;
        
        $('.form-search').attr('action', url);
        
        var href = $(this).attr('href');
        $.ajax({
            type: "GET",
            url: url,
            success: function(data){
                console.log($('.container-app', data)); 
                $(".container-app").html($('.container-app', data));
            }
        });
        
        return false;
    });


$('.checkboxall').live("click", function() {

    $(".checkboxall").toggle(
        function () {
            $('.checkbox-element').attr('checked', true);
        },
        function () {
            $('.checkbox-element').attr('checked', false);
        })
        });

    $('#validate').click(function() {
        $('.form').submit();
    });
    
    $('.ajax').live("click", function() {
        
        var href = $(this).attr('href');
        $.ajax({
            type: "GET",
            url: href,
            success: function(data){
                console.log($('.container-app', data)); 
                $(".container-app").html($('.container-app', data));
            }
        });
        
        return false;
    });
});