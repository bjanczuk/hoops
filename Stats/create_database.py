#!/usr/bin/env python2.7

import os
import sys
import json
import sqlite3

def convertSeason(s):
	if s[1] < '6':
	  return  "20" + s[1:].replace("'", "")
	else:
	  return "19" + s[1:].replace("'", "")

PLAYER_STATS = None
TEAMS = None

# Load the json file into a list
with open('player_info.json') as f:
		PLAYER_STATS = json.load(f)

with open('team_info.json') as f:
	TEAMS = json.load(f)


# Open a database named 'stats.db'
connection = sqlite3.connect("stats.db")
cursor = connection.cursor()

# Create the player table
sql_command = """
CREATE TABLE PLAYER ( 
player_id INTEGER PRIMARY KEY, 
name VARCHAR(30), 
position VARCHAR(2),
jersey_num INT(2),
draft_pos INT(2),
draft_year INT(4),
age INT(2),
experience INT(7),
height VARCHAR(4),
weight INT(3),
current_team VARCHAR(30),
college VARCHAR(20),
salary INT(12));"""

cursor.execute(sql_command)

# Insert each player's info into the table
id_count = 1
for p in PLAYER_STATS:
	format_str = """INSERT INTO PLAYER (player_id, name, position, jersey_num, draft_pos, draft_year, age, experience, height, weight, current_team, college, salary)
	VALUES ({id}, "{name}", "{position}", {jersey_num}, {draft_pos}, {draft_year}, {age}, {experience}, "{height}", {weight}, "{current_team}", "{college}", {salary});"""

	sql_command = format_str.format(name=p['name'].replace(".", ""), position=p['position'],
									jersey_num=p['jersey_num'], draft_pos=p['draft_pos'], draft_year=p['draft_year'],
									age=p['age'], experience=p['experience'],
    								height=p['height'], weight=p['weight'], current_team=p['current_team'],
    								college=p['college'], salary=p['salary'], id=id_count)
	
	cursor.execute(sql_command)
	id_count += 1

for info in ['salary', 'college']:
	sql_command = "UPDATE PLAYER set {} = NULL where {} = 'NULL'".format(info, info)
	cursor.execute(sql_command)

for info in ['jersey_num', 'draft_pos', 'draft_year']:
	sql_command = "UPDATE PLAYER set {} = NULL where {} = -1111".format(info, info)
	cursor.execute(sql_command)

for info in ['position']:
	sql_command = "UPDATE PLAYER set {} = 'SG' where {} = 'G'".format(info, info)
	cursor.execute(sql_command)
	sql_command = "UPDATE PLAYER set {} = 'PF' where {} = 'F'".format(info, info)
	cursor.execute(sql_command)

# Save changes
connection.commit()

# Create the season stats table
sql_command = """
CREATE TABLE SEASON_STATS ( 
season_id INTEGER PRIMARY KEY,
player_id INTEGER REFERENCES PLAYER(player_id),
name VARCHAR(30), 
season VARCHAR(8),
season_type VARCHAR(15),
team VARCHAR(5),
year_exp INT(2),
games_played INT(2),
games_started INT(2),
minutes FLOAT(5),
fgm FLOAT(5),
fga FLOAT(5),
fgp FLOAT(5),
tpm FLOAT(5),
tpa FLOAT(5),
tpp FLOAT(5),
ftm FLOAT(5),
fta FLOAT(5),
ftp FLOAT(5),
o_rebounds FLOAT(5),
d_rebounds FLOAT(5),
rebounds FLOAT(5),
assists FLOAT(5),
blocks FLOAT(5),
steals FLOAT(5),
fouls FLOAT(5),
turnovers FLOAT(5),
points FLOAT(5),
efg_pct FLOAT(5),
tsp FLOAT(5),
ftr FLOAT(5),
trb_pct FLOAT(5),
ast_pct FLOAT(5),
stl_pct FLOAT(5),
blk_pct FLOAT(5),
tov_pct FLOAT(5),
usg_pct FLOAT(5),
WS FLOAT(5),
WS_per FLOAT(5),
BPM FLOAT(5),
VORP FLOAT(5),
PER FLOAT(5));"""

cursor.execute(sql_command)


id_count = 1
for p in PLAYER_STATS:
	for season in p['stats']:
		for season_type in p['stats'][season]:
			s = p['stats'][season][season_type]
			if len(s) == 0:
				continue

			first_year = int(min([convertSeason(year)[:4] for year in p['stats']]))
			p['stats'][season][season_type]['year_exp'] = int(convertSeason(season)[:4]) - first_year

			format_str = """INSERT INTO SEASON_STATS (season_id, player_id, name, season, season_type, team, year_exp, games_played, games_started,
						minutes, fgm, fga, fgp, tpm, tpa, tpp, ftm, fta, ftp, o_rebounds, d_rebounds, rebounds, assists, blocks, steals,
						fouls, turnovers, points, efg_pct, tsp, ftr, trb_pct, ast_pct, stl_pct, blk_pct, tov_pct, usg_pct, PER, WS, WS_per, BPM, VORP)
			VALUES (NULL, {id}, "{name}", "{season}", "{season_type}", "{team}", {year_exp}, {games_played}, 
					{games_started}, {minutes}, {fgm}, {fga}, {fgp}, {tpm}, {tpa}, {tpp}, {ftm},
					{fta}, {ftp}, {o_rebounds}, {d_rebounds}, {rebounds}, {assists}, {blocks},
					{steals}, {fouls}, {turnovers}, {points}, {efg_pct}, {tsp}, {ftr}, {trb_pct}, {ast_pct},
					{stl_pct}, {blk_pct}, {tov_pct}, {usg_pct}, {PER}, {WS}, {WS_per}, {BPM}, {VORP});"""

			#print p['name'], season, season_type, '\n', s, '\n\n\n'
			# print p['name'].replace(".", ""), season, season_type, s

			sql_command = format_str.format(id=id_count, name=p['name'].replace(".", ""),
											season=convertSeason(season),
											season_type=season_type,
											team=s['team'],
											games_played=s['games_played'],
											games_started=s['games_started'],
											minutes=s['minutes'],
											year_exp=s['year_exp'],
											fgm=s['fgm'],
											fga=s['fga'],
											fgp=s['fg%'],
											tpm=s['3pm'],
											tpa=s['3pa'],
											tpp=s['3p%'],
											ftm=s['ftm'],
											fta=s['fta'],
											ftp=s['ft%'],
											o_rebounds=s['o_rebounds'],
											d_rebounds=s['d_rebounds'],
											rebounds=s['rebounds'],
											assists=s['assists'],
											blocks=s['blocks'],
											steals=s['steals'],
											fouls=s['fouls'],
											turnovers=s['turnovers'],
											points=s['points'],
											efg_pct=s['efg_pct'],
											tsp=s['tsp'],
											ftr=s['ftr'],
											trb_pct=s['trb_pct'],
											ast_pct=s['ast_pct'],
											stl_pct=s['stl_pct'],
											blk_pct=s['blk_pct'],
											tov_pct=s['tov_pct'],
											usg_pct=s['usg_pct'],
											PER=s['PER'],
											WS=s['WS'],
											WS_per=s['WS_per'],
											BPM=s['BPM'],
											VORP=s['VORP'])
			
			cursor.execute(sql_command)
	id_count += 1

for stat in ['efg_pct', 'tsp', 'ftr', 'trb_pct', 'ast_pct', 'stl_pct', 'blk_pct', 'tov_pct', 'usg_pct', 'PER', 'WS', 'WS_per', 'BPM', 'VORP']:
	sql_command = "UPDATE SEASON_STATS set {} = NULL where {} = -1111".format(stat, stat)
	cursor.execute(sql_command)

sql_command = "UPDATE SEASON_STATS set tpp = 0 where tpm = 0"
cursor.execute(sql_command)


# Create the team table
sql_command = """
CREATE TABLE TEAM ( 
team_name VARCHAR(30),
team_abbrev VARCHAR(4),
arena_name VARCHAR(25),
arena_description VARCHAR,
coach VARCHAR(25),
player1 VARCHAR(30),
player2 VARCHAR(30),
player3 VARCHAR(30),
player4 VARCHAR(30),
player5 VARCHAR(30),
player6 VARCHAR(30),
player7 VARCHAR(30),
player8 VARCHAR(30),
player9 VARCHAR(30),
player10 VARCHAR(30),
player11 VARCHAR(30),
player12 VARCHAR(30),
player13 VARCHAR(30),
player14 VARCHAR(30),
player15 VARCHAR(30),
player16 VARCHAR(30),
player17 VARCHAR(30),
player18 VARCHAR(30),
player19 VARCHAR(30),
player20 VARCHAR(30),
player21 VARCHAR(30));"""

cursor.execute(sql_command)

# Insert each player's info into the table
sentinel = '!!'
for t in TEAMS:
	format_str = """INSERT INTO TEAM (team_name, team_abbrev, arena_name, arena_description, coach, player1, player2,
				player3, player4, player5, player6, player7, player8, player9, player10, player11, player12, player13,
				player14, player15, player16, player17, player18, player19, player20, player21)
	VALUES ("{team_name}", "{team_abbrev}", "{arena_name}", "{arena_description}", "{coach}", """

	sql_command = format_str.format(team_name=t, team_abbrev=TEAMS[t]['abbrev'], arena_name=TEAMS[t]['arena_name'],
					arena_description=TEAMS[t]['arena_description'].replace('\"', '\''), coach=TEAMS[t]['coach'])

	# Insert the roster
	for i, player in enumerate(TEAMS[t]['players'], start=1):
		sql_command += "\"{}\", ".format(player).replace(".", "")

	# Fill in the remaining, blank players with a sentinel string
	for i in range(len(TEAMS[t]['players']) + 1, 22):
		sql_command += "\"{}\", ".format(sentinel)

	# Remove the last trailing space and comma and finish the command
	sql_command = sql_command.strip()[:-1] + ');'
	cursor.execute(sql_command)

	# Finally, update the sentinel strings to be NULL instead
	for i in range(1, 22):
		col = "player{}".format(i)
		sql_command = 'UPDATE TEAM set {} = NULL where {} = "{}"'.format(col, col, sentinel)
		cursor.execute(sql_command)

	cursor.execute(sql_command)

sql_command = 'UPDATE TEAM set arena_name = "Golden 1 Center" where team_name = "Sacramento Kings" '
cursor.execute(sql_command)
sql_command = """UPDATE TEAM set arena_description =
				"The Golden 1 Center is a multi-purpose indoor arena, located in Downtown Sacramento, California. It sits partially on the site of the former Downtown Plaza shopping center. The publicly owned arena is part of a business and entertainment district called Downtown Commons (DoCo), which includes a $250 million 16-story mixed-use tower.
\nThe arena, which replaced Sleep Train Arena as the home of the Sacramento Kings of the National Basketball Association, hosts concerts, conventions and other sporting and entertainment events. 34 luxury suites were sold to include all events year-round. Suite partners have access to three exclusive clubs on the premium level including two skyboxes that overlook the concourse and have a direct view of the outside. There are 48 loft-style suites. Capacity is expandable to about 19,000 to accommodate concert audiences."
				where team_name = "Sacramento Kings" """
cursor.execute(sql_command)

# Save changes
connection.commit()

# Save changes
connection.commit()

# Close the connection and exit
connection.close()