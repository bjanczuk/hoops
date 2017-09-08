#!/usr/bin/env python2.7

import os
import sys
import sqlite3

players = []

def print_cursor(cursor):
	for result in cursor.fetchall():
		for t in result:
			print t

# Open a database named 'stats.db'
connection = sqlite3.connect("stats.db")
cursor = connection.cursor()

# sql_command = """SELECT name, points, season, season_type FROM SEASON_STATS WHERE games_played = 82 and season = "'16-'17" """
# cursor.execute(sql_command)
# print_cursor(cursor)


# sql_command = """SELECT name, PER FROM SEASON_STATS WHERE PER = -1111 and season = "'16-'17" and season_type == 'regular_season'
# 				and minutes > 20 ORDER BY PER """
# cursor.execute(sql_command)
# print_cursor(cursor)

# sql_command = """ SELECT name, season, season_type FROM SEASON_STATS WHERE PER IS NULL """
# cursor.execute(sql_command)
# print_cursor(cursor)

# sql_command = """ SELECT name, experience FROM PLAYER WHERE experience > 10 """
# cursor.execute(sql_command)
# print_cursor(cursor)

sql_command = """ SELECT name FROM PLAYER ORDER BY LOWER(name) """
cursor.execute(sql_command)
print_cursor(cursor)

connection.close()
