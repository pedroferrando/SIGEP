/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function hoverTabla(componente){
    if(componente!="" && componente!=undefined){
        comp = componente;
    }else{
        comp = '.tablagenerica tr';
    }
    $(comp).hover(
        function() {
            $(this).addClass('hovered');
        }, function() {
            $(this).removeClass('hovered');
        }
    );
}