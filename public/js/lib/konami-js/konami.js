/*
 * Konami-JS
 * http://snaptortoise.com/konami-js
 * Copyright (c) 2009 George Mandis (georgemandis.com)
 * Version: 1.0 (04/27/2009)
 * Licensed under the Artistic License/GPL
 * http://dev.perl.org/licenses/
 * Tested in: Safari 4, Firefox 3, IE7
 */

var konami = {
        input:"",
        clear:setTimeout('konami.clear_input()',2000),
        load: function(link) {
                window.document.onkeyup = function(e) {
                        konami.input+= e ? e.keyCode : event.keyCode         
                        if (konami.input == "3838404037393739666513") {
                                konami.code(link)
                                clearTimeout(konami.clear)
                                }
                        clearTimeout(konami.clear)
                        konami.clear = setTimeout("konami.clear_input()",2000)
                        }
        },
        code: function(link) { window.location=link},
        clear_input: function() {
                konami.input="";
                clearTimeout(konami.clear);
        }
}

konami.code = function(){
	var key = 'weloveufrankie';
	//Effect.BlindUp('div_content', { duration: 3.0 });
	mywindow = window.open('/flash/konami/Steven-Erin-Frankie_NY-1984.php?key='+key,'vidpopupWindow','width=500,height=420,scrollbars');
	mywindow.moveTo(300,100);
	}
