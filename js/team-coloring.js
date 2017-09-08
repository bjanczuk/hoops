$(document).ready(function(){
    var team = $('.text_team_color').parents("div:eq(1)").attr("id");
    if (team != null) {
        team = team.substring(0, team.length - 4);   

        $('body:has(div#team_header_background)').css('background-color', team_pc[team]);
        $('body:has(h1#name_header)').css('background-color', '#EDEEF0');
        $('.text_team_color').css("color", team_sc[team]);
        $('.category').css("color", team_sc[team]);

        var css = 'table td:hover{ background-color:' +  team_sc[team] + '; color: white; }';
    	var style = document.createElement('style');

    	if (style.styleSheet) {
    	    style.styleSheet.cssText = css;
    	} else {
    	    style.appendChild(document.createTextNode(css));
    	}
    	document.getElementsByTagName('head')[0].appendChild(style);

        css = 'th.headerSortUp, th.headerSortDown { background-color:' +  team_pc[team] + '; color: white; }';
        style = document.createElement('style');

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.getElementsByTagName('head')[0].appendChild(style);

    	$('.header_link_text').css("color", team_sc[team]);

        css = '.header_filter_graph:hover .header_link_text { color: white !important; }';
        style = document.createElement('style');

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.getElementsByTagName('head')[0].appendChild(style);

        $('.headshot_info').css("border", 'solid ' + team_sc[team]);
        $('.headshot_info').css("background", 'white'); // fallback
        $('.headshot_info').css("background", '-webkit-linear-gradient(' + team_pc[team] + ', '+ team_gradient[team]); /* safari */
        $('.headshot_info').css("background", '-o-linear-gradient(' + team_pc[team] + ', '+ team_gradient[team]); /* opera */
        $('.headshot_info').css("background", '-moz-linear-gradient(' + team_pc[team] + ', '+ team_gradient[team]); /* firefox */
        $('.headshot_info').css("background", '-linear-gradient(' + team_pc[team] + ', '+ team_gradient[team]); /* standard */

        $('.table_header').css("color", team_sc[team]);
        $('.no_table').css("color", team_sc[team]);
        var css = 'button.team_color_button:active { background: ' + team_pc[team] + '; color:white; }';
        var style = document.createElement('style');

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.getElementsByTagName('head')[0].appendChild(style);
        
        $('#arena_name_text').css("-moz-text-decoration-color", team_sc[team]); /* firefox */
        $('#arena_name_text').css("text-decoration-color", team_sc[team]); /* standard */

        $("<style>").prop("type", "text/css").html("\
            .autocomplete-suggestions strong {color: " + team_bright[team] + "; }").appendTo("head");

        $('#header').css("background", 'white'); // fallback
        $('#header').css("background", '-webkit-linear-gradient(' + team_gradient[team] + ', '+ team_pc[team]); /* safari */
        $('#header').css("background", '-o-linear-gradient(' + team_gradient[team] + ', '+ team_pc[team]); /* opera */
        $('#header').css("background", '-moz-linear-gradient(' + team_gradient[team] + ', '+ team_pc[team]); /* firefox */
        $('#header').css("background", '-linear-gradient(' + team_gradient[team] + ', '+ team_pc[team]); /* standard */
    }

});