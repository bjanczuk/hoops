var team_pc = []
var team_sc = []

team_pc['atlanta_hawks'] = "#E03A3E";
team_sc['atlanta_hawks'] = "#C1D32F";

team_pc['boston_celtics'] = "#008348";
team_sc['boston_celtics'] = "#BB9753";

team_pc['brooklyn_nets'] = "#000000";
team_sc['brooklyn_nets'] = "#000000";

team_pc['charlotte_hornets'] = "#1D1160";
team_sc['charlotte_hornets'] = "#00788C";

team_pc['chicago_bulls'] = "#CE1141";
team_sc['chicago_bulls'] = "#000000";

team_pc['cleveland_cavaliers'] = "#6F263D";
team_sc['cleveland_cavaliers'] = "#FFB81C";

team_pc['dallas_mavericks'] = "#0053BC";
team_sc['dallas_mavericks'] = "#000000";

team_pc['denver_nuggets'] = "#5091CD";
team_sc['denver_nuggets'] = "#FDB927";

team_pc['detroit_pistons'] = "#006BB6";
team_sc['detroit_pistons'] = "#ED174C";

team_pc['golden_state_warriors'] = "#006BB6";
team_sc['golden_state_warriors'] = "#FDB927";

team_pc['houston_rockets'] = "#CE1141";
team_sc['houston_rockets'] = "#8C8B8A";

team_pc['indiana_pacers'] = "#002D62";
team_sc['indiana_pacers'] = "#FDBB30";

team_pc['los_angeles_clippers'] = "#ED174C";
team_sc['los_angeles_clippers'] = "#006BB6";

team_pc['los_angeles_lakers'] = "#552583";
team_sc['los_angeles_lakers'] = "#FDB927";

team_pc['memphis_grizzlies'] = "#00285E";
team_sc['memphis_grizzlies'] = "#6189B9";

team_pc['miami_heat'] = "#98002E";
team_sc['miami_heat'] = "#000000";

team_pc['milwaukee_bucks'] = "#00471B";
team_sc['milwaukee_bucks'] = "#EEE1C6";

team_pc['minnesota_timberwolves'] = "#005084";
team_sc['minnesota_timberwolves'] = "#7AC142";

team_pc['new_orleans_pelicans'] = "#01295D";
team_sc['new_orleans_pelicans'] = "#E81333";

team_pc['new_york_knicks'] = "#006BB6";
team_sc['new_york_knicks'] = "#F58426";

team_pc['oklahoma_city_thunder'] = "#0A7EC2";
team_sc['oklahoma_city_thunder'] = "#F05333";

team_pc['orlando_magic'] = "#0077C0";
team_sc['orlando_magic'] = "#000000";

team_pc['philadelphia_76ers'] = "#006BB6";
team_sc['philadelphia_76ers'] = "#ED174C";

team_pc['phoenix_suns'] = "#E56020";
team_sc['phoenix_suns'] = "#3E2680";

team_pc['portland_trail_blazers'] = "#E03A3E";
team_sc['portland_trail_blazers'] = "#000000";

team_pc['sacramento_kings'] = "#5A2D81";
team_sc['sacramento_kings'] = "#63727A";

team_pc['san_antonio_spurs'] = "#000000";
team_sc['san_antonio_spurs'] = "#63727A";

team_pc['toronto_raptors'] = "#CE1141";
team_sc['toronto_raptors'] = "#000000";

team_pc['utah_jazz'] = "#002B5C";
team_sc['utah_jazz'] = "#F9A01B";

team_pc['washington_wizards'] = "#E31837";
team_sc['washington_wizards'] = "#002B5C";


$(document).ready(function(){
    var team = $('.text_team_color').parents("div:eq(1)").attr("id");
    team = team.substring(0, team.length - 4);
    $('body:has(div#team_header_background)').css('background-color', team_pc[team]);
    $('.text_team_color').css("color", team_sc[team]);
    $('.slick-prev:before').css("color", team_sc[team]);
    $('.slick-next:before').css("color", team_sc[team]);
});