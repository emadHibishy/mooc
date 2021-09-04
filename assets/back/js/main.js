$(function(){
    $('.add_sec').on('click', function(){
        $(this).fadeOut(300).next('.section_form').fadeIn(700);
    });


    /*===================
    * =     Delete      =
    * ===================
    */
    $('button.delete-content').on('click',function(){
        $($(this).data('target')).fadeIn().children('.card').slideDown(700);
    });
    $('.cancel').on('click',function(){
        $(this).parents('.card').slideUp(700).parent().fadeOut();   
    });
});