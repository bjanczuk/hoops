// Pause the script for a fraction of a second to make sure the DB gets loaded correctly
// setTimeout(function(){}, 400);

var result_txt = ''; 
var name_matches = []

$(function(){
    var names = JSON.parse(sessionStorage.getItem("names"));
    var teams = ['Atlanta Hawks', 'Boston Celtics', 'Charlotte Hornets', 'Chicago Bulls', 'Cleveland Cavaliers', 'Dallas Mavericks',
    'Denver Nuggets', 'Detroit Pistons', 'Golden State Warriors', 'Houston Rockets', 'Indiana Pacers', 'Los Angeles Clippers',
    'Los Angeles Lakers', 'Memphis Grizzlies', 'Miami Heat', 'Milwaukee Bucks', 'Minnesota Timberwolves', 'Brooklyn Nets',
    'New Orleans Pelicans', 'New York Knicks', 'Oklahoma City Thunder', 'Orlando Magic', 'Philadelphia 76ers', 'Phoenix Suns',
    'Portland Trail Blazers', 'Sacramento Kings', 'San Antonio Spurs', 'Toronto Raptors', 'Utah Jazz', 'Washington Wizards'];
    
    // setup autocomplete function pulling from the names and teams arrays
    $('#autocomplete').autocomplete({
        lookup: names.concat(teams),
        onSelect: function (suggestion) {
            var thehtml = '<strong>Currency Name:</strong> ' + suggestion.value + ' <br> <strong>Symbol:</strong> ' + suggestion.type;
            $('#outputcontent').html(thehtml);
        }
    });
});