#!/usr/bin/env python2.7

import os
import sys
import requests
import re
import json
import sqlite3
import time
import os.path
from collections import defaultdict

headers = {'user-agent': 'espn-{}'.format(os.environ['USER'])}

TEAMS = dict()
PLAYERS = []
REGULAR_SCORERS = []
PLAYOFF_SCORERS = []
PER_STANDINGS = []

def get_team_name(find):
	global TEAMS

	team_words = find.split('/')[-1].split('-')
	team = ''
	for word in team_words:
		if word == 'la':
			team += 'Los Angeles'
		else:
			team += word.capitalize()
		team += ' '
	team = str(team.strip())
	TEAMS[team] = {'abbrev': None, 'arena_name': None, 'arena_description': None, 'players': [], 'coach': None}
	return team

def get_roster(find, team):
	global PLAYERS, TEAMS

	url_end = find.split('/')[-2] + '/' + find.split('/')[-1]
	TEAMS[team]['abbrev'] = find.split('/')[-2]

	# get information about the team's arena
	get_arena_info(team, TEAMS[team]['abbrev'])

	# next, get the team's roster information
	roster_URL = "http://www.espn.com/nba/team/roster/_/name/" + url_end
	roster_r = requests.get(roster_URL)

	finds = re.findall('\/nba\/player\/_\/id\/.+?(?=\/)\/.+?(?=\">).+?(?=<\/tr)', str(roster_r.text))
	for player_find in finds:
		player_regex = re.search('\/nba\/player\/_\/id\/.+?(?=\/)\/.+?(?=\">)\">(.+?)(?=</a>).+?(?=<td>)<td>(.+?)(?=<\/td>).+?(?=<td >)<td >(.+?)(?=<\/td>).+?(?=<td >)<td >(.+?)(?=<\/td>).+?(?=<td >)<td >(.+?)(?=<\/td>).+?(?=<td>)<td>(.+?)(?=<\/td>).*>(.*)[;|<]', player_find)
		groups = player_regex.groups()

		player_info = {'name': str(groups[0].encode('utf-8')), 'position': str(groups[1].encode('utf-8')),
					   'age': str(groups[2].encode('utf-8')), 'height': str(groups[3].encode('utf-8')),
					   'weight': groups[4].encode('utf-8'), 'current_team': team}

		# avoid the information that has no value
		player_info['college'] = 'NULL' if 'nbsp' in groups[5] else str(groups[5].encode('utf-8'))
		player_info['salary'] = 'NULL' if 'nbsp' in groups[6] else int(str(groups[6].encode('utf-8')).replace('$', '').replace(',', ''))

		# add the player's per-season stats, as a dict of stat dicts, to his overall dict
		tup, player_info['stats'] = get_player_stats(player_find, player_info['name'])
		player_info['jersey_num'], player_info['draft_pos'], player_info['draft_year'], player_info['experience'] = tup
		PLAYERS.append(player_info)

		# add the player to his team's roster
		TEAMS[team]['players'].append(str(groups[0].encode('utf-8')))

	# finally, get the coach's name
	finds = re.findall('Coach:.+?\">(.+?)<', str(roster_r.text))
	try:	
		TEAMS[team]['coach'] = finds[0].strip()
	except IndexError:
		print roster_URL
		sys.exit()

def get_arena_info(team, team_short):
	arena = dict()
	arena_url = "http://www.espn.com/nba/team/stadium/_/name/" + team_short
	arena_r = requests.get(arena_url)

	finds = re.findall('<\/script><title>(.+?)Seating Chart', str(arena_r.text))
	TEAMS[team]['arena_name'] = finds[0].strip()

	finds = re.findall('HISTORY:<\/h4>(<p>)+(.+?)<', str(arena_r.text))
	try:
		TEAMS[team]['arena_description'] = finds[-1][-1].strip()
	except IndexError:
		print team, team_short, arena_url
		sys.exit()

def get_player_stats(player_find, name):
	player_stats = defaultdict(dict)
	stat_lists = defaultdict(dict)
	season_types = ['regular_season', 'playoffs']
	experience = 0

	url_base = re.search('(.+?)(?=\")', player_find)
	player_id = url_base.group(0).split('/')[-2]
	regular_url = 'http://www.espn.com/nba/player/stats/_/id/{}/seasontype/1'.format(player_id)
	playoff_url = 'http://www.espn.com/nba/player/stats/_/id/{}/seasontype/3'.format(player_id)

	for i, URL in enumerate([regular_url, playoff_url]): # use 'i' to distinguish between regular season and playoffs
		stats_r = requests.get(URL, headers=headers)

		# First, get the player's draft pos./year, jersey number,
		# experience, and headshot image from his stats page
		if i == 0:
			jersey_regex = re.findall('<ul class="general-info" ><li class="first">(.+?)<', str(stats_r.text))
			try:
				if len(jersey_regex) > 0:
					jersey_num = int(jersey_regex[0].split()[0][1:])
				else:
					jersey_num = -1111
			except ValueError:
				jersey_num = -1111

			draft_regex = re.findall('<span>Drafted<\/span>(.+?)<', str(stats_r.text))
			if len(draft_regex) > 0:
				draft_pos = int(draft_regex[0].split()[3][:-2])
				draft_year = draft_regex[0].split()[0][:-1]
			else:
				draft_pos = -1111
				draft_year = -1111

			exp_regex = re.findall('<span>Experience<\/span>(.+?)<', str(stats_r.text))
			if len(exp_regex) > 0:
				experience = int(exp_regex[0].split()[0])

			img_regex = re.findall('main-headshot\"><img src=\"(.+?)\"', str(stats_r.text))
			if len(img_regex) > 0:
				try:
					print '\t', name.replace(' ', '_').replace("'", "")
					os.system("curl -s -o ./headshots/{}.png {}".format(name.replace(' ', '_').replace("'", "").replace(".", "").lower(), img_regex[0]))
				except ConnectionError:
					print '\t', img_regex[0], '\t', name

		# Then move on to the stats themselves
		stats_regex = re.findall('(\'\d\d-\'\d\d).+?([A-Z]{2,6}).+?([\d]{1,4}).+?([\d]{1,4})<\/td>(.+?)(?=<\/td></tr>)', str(stats_r.text))
		if len(stats_regex) == 0:
			continue

		third = len(stats_regex) / 3 # we only care about the top 1/3 of stats for each player, i.e. the averages
		for season_summary in stats_regex[0:third]:
			season = season_summary[0]

			# instantiate each season ('09-'10, '13-'14, etc.) as a dict with two season_types:
			# 'regular season' and 'playoffs', both of which are dicts themselves
			# Use one temporary dict, which stores every season as a list of stats, and a second, final
			# dict that will store the final averages
			if season not in player_stats:
				stat_lists[season] = dict((season_types[k], defaultdict(list)) for k in range(2))
				player_stats[season] = dict((season_types[k], dict()) for k in range(2))

			stat_lists[season][season_types[i]]['team'].append(season_summary[1])
			stat_lists[season][season_types[i]]['games_played'].append(int(season_summary[2]))
			stat_lists[season][season_types[i]]['games_started'].append(int(season_summary[3]))

			box_score_stats = []
			# scrape the grouping of stats found in each HTML chunk of right-aligned text
			for stat in season_summary[4].split('<td style="text-align:right;">')[1:]:
				if stat.endswith('</td>'):
					stat = stat[:-5]
				box_score_stats.append(stat)

			stat_lists[season][season_types[i]]['minutes'].append(float(box_score_stats[0]))
			stat_lists[season][season_types[i]]['fgm'].append(float(box_score_stats[1].split('-')[0]))
			stat_lists[season][season_types[i]]['fga'].append(float(box_score_stats[1].split('-')[1]))
			stat_lists[season][season_types[i]]['fg%'].append(float(box_score_stats[2]))
			stat_lists[season][season_types[i]]['3pm'].append(float(box_score_stats[3].split('-')[0]))
			stat_lists[season][season_types[i]]['3pa'].append(float(box_score_stats[3].split('-')[1]))
			stat_lists[season][season_types[i]]['3p%'].append(float(box_score_stats[4]))
			stat_lists[season][season_types[i]]['ftm'].append(float(box_score_stats[5].split('-')[0]))
			stat_lists[season][season_types[i]]['fta'].append(float(box_score_stats[5].split('-')[1]))
			stat_lists[season][season_types[i]]['ft%'].append(float(box_score_stats[6]))
			stat_lists[season][season_types[i]]['o_rebounds'].append(float(box_score_stats[7]))
			stat_lists[season][season_types[i]]['d_rebounds'].append(float(box_score_stats[8]))
			stat_lists[season][season_types[i]]['rebounds'].append(float(box_score_stats[9]))
			stat_lists[season][season_types[i]]['assists'].append(float(box_score_stats[10]))
			stat_lists[season][season_types[i]]['blocks'].append(float(box_score_stats[11]))
			stat_lists[season][season_types[i]]['steals'].append(float(box_score_stats[12]))
			stat_lists[season][season_types[i]]['fouls'].append(float(box_score_stats[13]))
			stat_lists[season][season_types[i]]['turnovers'].append(float(box_score_stats[14]))
			stat_lists[season][season_types[i]]['points'].append(float(box_score_stats[15]))

	for season in stat_lists:
		for season_type in stat_lists[season]:
			if len(stat_lists[season][season_type]) == 0:
				continue

			# Select the team for this player's season as the team he played the most games for
			index = 0
			for i, played in enumerate(stat_lists[season][season_type]['games_played']):
				if played == max(stat_lists[season][season_type]['games_played']):
					index = i
					break
			player_stats[season][season_type]['team'] = stat_lists[season][season_type]['team'][index]

			# Sum up all of the games he played and started as his overall 'games_played' and 'games_started'
			player_stats[season][season_type]['games_played'] = sum(stat_lists[season][season_type]['games_played'])
			player_stats[season][season_type]['games_started'] = sum(stat_lists[season][season_type]['games_started'])

			# Average the rest of the stats, weighted by games played, to determine overall season stats
			for key in ['minutes', 'fga', 'fgm', 'fg%', '3pm', '3pa', '3p%', 'ftm', 'fta', 'ft%',
						'o_rebounds', 'd_rebounds', 'rebounds', 'assists', 'blocks', 'steals', 'fouls',
						'turnovers', 'points']:
				s = 0
				for i, stat_listing in enumerate(stat_lists[season][season_type][key]):
					s += (stat_listing * stat_lists[season][season_type]['games_played'][i])

				if player_stats[season][season_type]['games_played'] != 0:
					player_stats[season][season_type][key] = round(s / float(player_stats[season][season_type]['games_played']), 2)
				else:
					player_stats[season][season_type][key] = 0

			if season == "'16-'17":
				if season_type == 'regular_season':
					REGULAR_SCORERS.append((player_stats[season][season_type]['points'], url_base.group(0).split('/')[-1]))
				else:
					PLAYOFF_SCORERS.append((player_stats[season][season_type]['points'], url_base.group(0).split('/')[-1]))

	# Use basketball-reference.com to find the player's advanced stats for every regular season and playoffs he's played in
	if len(player_stats) > 0:
		advanced_stats = get_BR_stats(player_stats, name)
		for season in player_stats:
			for season_type in player_stats[season]:
				if len(player_stats[season][season_type]) == 0:
					continue

				for stat in ['PER', 'efg_pct', 'tsp', 'ftr', 'trb_pct', 'ast_pct', 'stl_pct', 'blk_pct', 'tov_pct', 'usg_pct', 'WS', 'WS_per', 'BPM', 'VORP']:
					if season not in advanced_stats:
						advanced_stats[season] = dict()
					if season_type not in advanced_stats[season]:
						advanced_stats[season][season_type] = dict()

					if stat in advanced_stats[season][season_type]:
						player_stats[season][season_type][stat] = advanced_stats[season][season_type][stat]
					else:
						player_stats[season][season_type][stat] = -1111

	# Keep track of everybody's PER
	if "'16-'17" in player_stats and 'PER' in player_stats["'16-'17"]['regular_season'] and player_stats["'16-'17"]['regular_season']['minutes'] > 20:
		PER_STANDINGS.append((player_stats["'16-'17"]['regular_season']['PER'], name))

	return (jersey_num, draft_pos, draft_year, experience), player_stats

def get_BR_stats(player_stats, name):
	season_advanced_stats = dict()

	if "'16-'17" not in player_stats:
		return season_advanced_stats

	player_url = get_BR_player_url(convert_ESPN_to_BR(player_stats["'16-'17"]['regular_season']['team']), name)
	if player_url != '':
		player_r = requests.get(player_url, headers=headers)

		# Get regular season and playoff advanced stats
		for insert in ['', 'playoffs_']:
			season_type = 'regular_season' if insert == '' else 'playoffs'

			for match in re.findall('<tr id=\"{}advanced\..+?<\/td><\/tr>'.format(insert), str(player_r.text.encode('utf-8'))):
				season = re.findall('[\d]+-[\d]+', match)[0]
				season = "\'" + season.split('-')[0][-2:] + "-\'" + season.split('-')[1] # Convert to the format used elsewhere

				# Only add advanced stats for the portion of the season in which the player played with his 'main' team
				match_team = re.findall('data-stat="team_id" >(<strong>)?.+?(?=>)>(.+?)<', match)[0][-1]
				if match_team == convert_ESPN_to_BR(player_stats[season][season_type]['team']) or \
					((match_team == 'NOH' or match_team == 'NOK') and player_stats[season][season_type]['team'] == 'NO') or \
					(match_team == 'CHA' and player_stats[season][season_type]['team'] == 'CHA'):
					if season not in season_advanced_stats:
						season_advanced_stats[season] = {'regular_season': dict(), 'playoffs': dict()}

					season_advanced_stats[season][season_type] = {'efg_pct': -1111, 'tsp': -1111, 'ftr': -1111,
																  'trb_pct': -1111, 'ast_pct': -1111,
																  'stl_pct': -1111, 'blk_pct': -1111,
																  'tov_pct': -1111, 'usg_pct': -1111,
																  'WS': -1111, 'WS_per': -1111, 'BPM': -1111,
																  'VORP': -1111, 'PER': -1111}

					# List each stat as the name it's given in the website's HTML
					html_inserts = {'PER': 'per', 'tsp': 'ts_pct', 'ftr': 'fta_per_fga_pct',
								    'trb_pct': 'trb_pct', 'ast_pct': 'ast_pct',
								    'stl_pct': 'stl_pct', 'blk_pct': 'blk_pct',
								    'tov_pct': 'tov_pct', 'usg_pct': 'usg_pct',
								    'WS': 'ws', 'WS_per': 'ws_per_48', 'BPM': 'bpm', 'VORP': 'vorp'}
					
					# Loop through the various stats and try each one (some players have certain stats missing)
					for k in html_inserts:
						try:
							season_advanced_stats[season][season_type][k] = \
								float(re.findall('data-stat="{}" >(<strong>)?(.+?)<'.format(html_inserts[k]), match)[-1][-1])
						except ValueError:
							continue
			
			# also scrape the player's effective field goal % for each season
			for match in re.findall('<tr id=\"{}totals\..+?<\/td><\/tr>'.format(insert), str(player_r.text.encode('utf-8'))):
				season = re.findall('[\d]+-[\d]+', match)[0]
				season = "\'" + season.split('-')[0][-2:] + "-\'" + season.split('-')[1] # Convert to the format used elsewhere 

				match_team = re.findall('data-stat="team_id" >(<strong>)?.+?(?=>)>(.+?)<', match)[0][-1]
				if match_team == convert_ESPN_to_BR(player_stats[season][season_type]['team']) or \
					((match_team == 'NOH' or match_team == 'NOK') and player_stats[season][season_type]['team'] == 'NO') or \
					(match_team == 'CHA' and player_stats[season][season_type]['team'] == 'CHA'):
					try:
						season_advanced_stats[season][season_type]['efg_pct'] = \
							float(re.findall('data-stat="efg_pct" >(<strong>)?(.+?)<', match)[-1][-1])
						# print name, season, season_type, season_advanced_stats[season][season_type]['efg_pct']
					except ValueError:
						continue
					except KeyError:
						print 'UH OH'
						print name, season, season_type, float(re.findall('data-stat="efg_pct" >(<strong>)?(.+?)<', match)[-1][-1])
						sys.exit()

	return season_advanced_stats

def get_BR_player_url(team, name):
	team_url = 'http://www.basketball-reference.com/teams/{}/2017.html'.format(team)
	team_r = requests.get(team_url, headers=headers)

	roster = re.findall('<tr ><th.+?(?=<\/tbody><\/table>)', str(team_r.text.encode('utf-8')).replace('\n', ' '))[0]

	# Look through the given info of each player to find the one currently being updated
	for match in re.findall('href="(\/players.+?(?=\"))\">(.+?)<', roster):
		if name == match[1] or name.split()[-1] == match[1].split()[-1] and all(char in match[1] for char in name) or \
			match[1] in name or name in match[1]:
			return 'http://www.basketball-reference.com' + match[0]
	return ''

def convert_ESPN_to_BR(team):
	# Convert between the ESPN team headings and the BR team headings if need be
	if team == 'UTAH':
		team = 'UTA'
	elif team == 'BKN':
		team = 'BRK'
	elif team == 'NY':
		team = 'NYK'
	elif team == 'GS':
		team = 'GSW'
	elif team == 'WSH':
		team = 'WAS'
	elif team == 'PHX':
		team = 'PHO'
	elif team == 'NO':
		team = 'NOP'
	elif team == 'CHA':
		team = 'CHO'
	elif team == 'SA':
		team = 'SAS'
	elif team == 'NJ':
		team = 'NJN'

	return team

def getMissingHeadshots():
	for player in PLAYERS:
		name = player['name']
		hs_path = "./headshots/{}.png".format(name.replace(' ', '_').replace("'", "").replace(".", "").lower())
		if os.path.isfile(hs_path):
			continue

		try:
			nba_player_url = "http://www.nba.com/players/{}/{}".format(name.split()[0], name.split()[1])
			r = requests.get(nba_player_url, headers=headers)

			hs_url = getHeadshotURL(r.text)
			if hs_url != "":
				os.system("curl -s -o ./headshots/{}.png {}".format(name.replace(' ', '_').replace("'", "").replace(".", "").lower(), hs_url))
				print '\t\t', name
		except:
			print "NBA HEADSHOT ERROR -->", player['name']
			print nba_player_url
			print hs_url
			print type(exception).__name__
			sys.exit()

def getHeadshotURL(text):
	hs_regex = "src=\"(.*?headshots.*?png)"
	match = re.search(hs_regex, text)
	if match != None:
	  return "https:" + match.groups(1)[0]
	else:
		print "COULDNT FIND FOR", name
		return ""

# Main
URL = "http://espn.go.com/nba/teams"
r = requests.get(URL, headers=headers)

finds = re.findall('\/nba\/team\/_\/name/.+?(?=\")', str(r.text))
for find in finds:
	team = get_team_name(find)
	get_roster(find, team)

print "MISSING HEADSHOTS:"
getMissingHeadshots()


# Find the top scorers from the '16-'17 season
print "POINTS - REGULAR SEASON:"
for player in sorted(REGULAR_SCORERS, key=lambda tup: tup[0])[-10:]:
	print '\t', player

# Find the top scorers from the '16-'17 season
print 'POINTS - PLAYOFFS:'
for player in sorted(PLAYOFF_SCORERS, key=lambda tup: tup[0])[-10:]:
	print '\t', player

# Find the top PERs from '16-'17 regular season
print 'PER - REGULAR SEASON:'
for player in sorted(PER_STANDINGS, key=lambda tup: tup[0])[-10:]:
	print '\t', player

# Save the player information in a file
with open('player_info.json', 'w') as f:
    json.dump(PLAYERS, f)

# Save the team information in a file
with open('team_info.json', 'w') as f:
    json.dump(TEAMS, f)