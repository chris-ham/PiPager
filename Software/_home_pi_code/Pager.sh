#!/usr/bin/env python

#this app assumes the following
#1 - there is a file in the root of the only USB thumb drive called 'Banks.txt'
#	this file contains a list of the pager bank identifiers in the form
#	WWW1010101010101010 where WWW is the color band identifier that the human
#	looks at to identify the pager bank and 1010101010101010 is the unique
#	code that identifies the pager bank
#
#2 - there is a directory for each division that contains the files
#	Matches.csv - a direct output from VEX TM
#	TeamList.csv - a direct output from VEX TM where the sponsor has been
#		modified to represent the pager allocated to the team ie WWW1

import serial
import os
import subprocess
import time
import MySQLdb

#Pager Bank definitions
#PagerID is the three letter ID code of the Pager bank
#ie WWW = White White White
#PagerCode is the code for the pager bank ie 101101001
PagerID = []
PagerCode = []


Team = []
Match = []

#initialise the serial port that the Pager transmitter is plugged into
port = serial.Serial("/dev/ttyACM0", baudrate=9600, timeout=3.0)

#***************************************
def get_USB_Name():
	global USB_name
	#get the name of the USB stick
	rpistr = "ls /media/pi"
	proc = subprocess.Popen(rpistr, shell=True, preexec_fn=os.setsid, stdout=subprocess.PIPE)
	line = proc.stdout.readline()
	USB_name = line.rstrip()
	print USB_name

#***************************************
#loads the pager reference into memory that will be used
#to translate WWW into 101010101... etc
#***************************************
def load_pager_definitions():
	#open the file that contains the pager bank definitions
	#in read only mode but wait for it to exist first
	while (os.path.exists('/media/pi/'+ USB_name +'/Banks.txt') == False):
		time.sleep(0.1)
	f = open('/media/pi/'+ USB_name +'/Banks.txt','r')
	#read in the pager bank definitions and create an array of them
	global PagerCode
	global PagerID
	for line in f:
	  PagerID.append(line[:3])
	  PagerCode.append(line[3:19])


#***************************************
#gets that data associated with the specified match in a division
#***************************************
def get_match(aDivision, aMatch):
	#open the file that contains the team definitions
	#in read only mode
	f = open('/media/pi/'+ USB_name + '/' + aDivision + '/Matches.csv','r')

	Match = []
	global Division
	global MatchNum
	global Red1
	global Red2
	global Red3
	global Blue1
	global Blue2
	global Blue3
	Division = 0
	MatchNum = 0
	Red1 = 0
	Red2 = 0
	Red3 = 0
	Blue1 = 0
	Blue2 = 0
	Blue3 = 0
	for line in f:
		Match = line.split(',')
		if (Match[3] == aMatch):
			Division = Match[0]
			MatchNum = Match[3]
			Red1 = Match[5]
			Red2 = Match[6]
			Red3 = Match[7]
			Blue1 = Match[8]
			Blue2 = Match[9]
			Blue3 = Match[10]


#***************************************
#gets the data associated with a team in a cretain division
#***************************************
def get_team(aDivision,aTeam):
	#open the file that contains the team definitions
	#in read only mode
	f = open('/media/pi/'+ USB_name + '/' + aDivision + '/TeamList.csv','r')

	Team = []
	global TeamNum
	global TeamPagerBank
	global TeamPager
	TeamPagerBank = 'XXX'
	TeamPager = '0'
	for line in f:
		Team = line.split(',')
		if (Team[0] == aTeam):
			TeamNum = Team[0]
			TeamPagerBank = Team[7][:3]  #eg WWW
			TeamPager = Team[7][3:]      #eg 1
	if (TeamPagerBank != ''):
	 	print 'Page team = ' + TeamPagerBank + '-' + TeamPager
	else:
		print 'Team has no pager assigned to it'
		TeamPagerBank = 'WWW'
		TeamPager = '1'

#***************************************
#converts the number (string) representing the pager
#number into a reversed binary representation of it
#***************************************
def get_pager_num_code(aNum):
	code = '{0:09b}'.format(int(aNum)) #create binary representation
        code = code[::-1]    #reverse it
	return code

#***************************************
#page a particular team
#creates the full pager message and sends it for one team
#eg division VRC_HS_1, team 1
#	send_team_pager_msg("VRC_HS_1","1")
#***************************************
def send_team_pager_msg(aDivision, aTeam):
	get_team(aDivision, aTeam)
	pager_msg = '?6'
	pager_msg = pager_msg + PagerCode[PagerID.index(TeamPagerBank)]
        pager_msg = pager_msg + get_pager_num_code(TeamPager)
	pager_msg = pager_msg + '\n'
	#port.write("?61010100000111011010000000\n")
	port.write(pager_msg)
	print 'Pager Msg = ' + pager_msg

#***************************************
#pages all teams in a match
#gets the teams for a match in a division and pages them
#eg division VRC_HS_1, match 13
#	send_match_pages_msgs("VRC_HS_1","13")
#***************************************
def send_match_pages_msgs(aDivision,aMatch):
	#find the match of interest
	get_match(aDivision,aMatch)
	
	#get the teams involved one at a time
	send_team_pager_msg(aDivision,Red1)
	send_team_pager_msg(aDivision,Red2)
	send_team_pager_msg(aDivision,Red3)
	send_team_pager_msg(aDivision,Blue1)
	send_team_pager_msg(aDivision,Blue2)
	send_team_pager_msg(aDivision,Blue3)

#***************************************
#***************************************
def test_db():
	db = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor = db.cursor()
	cursor.execute("SELECT VERSION()")
	data = cursor.fetchone()
	print "Database version : %s " % data
	db.close()

#***************************************
#parses the UBS fob and adds the matches it finds to the SQL database
#***************************************
def add_all_matches_to_db():
	global match_index
	match_index = 0
	for aDivision,j,y in os.walk('/media/pi/'+ USB_name):
		if (not("Software") in aDivision) and (not("System") in aDivision):
			if (aDivision != '/media/pi/'+ USB_name):
				print aDivision
				add_division_matches_to_DB(aDivision)
	
#***************************************
#parses the UBS fob and adds the teams it finds to the SQL database
#***************************************
def add_all_teams_to_db():
	global team_index
	team_index = 0
	for aDivision,j,y in os.walk('/media/pi/'+ USB_name):
		if (not("Software") in aDivision) and (not("System") in aDivision):
			if (aDivision != '/media/pi/'+ USB_name):
				print aDivision
				add_teams_to_DB(aDivision)

		

#***************************************
#***************************************
def add_division_matches_to_DB(aDivision):
	#open the file that contains the match definitions
	#in read only mode
	f = open(aDivision + '/Matches.csv','r')
	aDivision = aDivision.replace('/media/pi/'+ USB_name + '/','')

	db2 = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor2 = db2.cursor()
	Match = []
	global Division
	global MatchNum
	global match_index
	global Red1
	global Red2
	global Red3
	global Blue1
	global Blue2
	global Blue3
	Division = 0
	MatchNum = 0
	Red1 = 0
	Red2 = 0
	Red3 = 0
	Blue1 = 0
	Blue2 = 0
	Blue3 = 0
	first_line = True
	for line in f:
		if (first_line != True):  #dont add first lines as ther are headings
			Match = line.split(',')
			Division = Match[0]
			MatchNum = Match[3]
			Red1 = Match[5]
			Red2 = Match[6]
			Red3 = Match[7]
			Blue1 = Match[8]
			Blue2 = Match[9]
			Blue3 = Match[10]
			sql2 = "INSERT INTO Matches \
				(seq, Division,MatchNumber,Red1, Red2, Red3, Blue1, Blue2, Blue3, UID) \
				VALUES \
				(%s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')" % \
				(match_index, aDivision, MatchNum, Red1, Red2, Red3, Blue1, Blue2, Blue3, aDivision+MatchNum)
			print sql2
			try:
				cursor2.execute(sql2)
				db2.commit()
				print "Insert OK"
			except:
				db2.rollback()
				print "Insert Error "
			match_index = match_index +1
		first_line = False
	db2.close()

#***************************************
#***************************************
def add_teams_to_DB(aDivision):
	#open the file that contains the team definitions
	#in read only mode
	f = open(aDivision + '/TeamList.csv','r')
	aDivision = aDivision.replace('/media/pi/'+ USB_name + '/','')

	db2 = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor2 = db2.cursor()
	Team = []
	global team_index
	global TeamNum
	global TeamPagerBank
	global TeamName
	global TeamPager
	TeamPagerBank = 'XXX'
	TeamPager = '0'

	first_line = True
	for line in f:
		if (first_line != True):  #dont add first lines as ther are headings

			Team = line.split(',')
			TeamNum = Team[0]
			TeamName = Team[1]
			TeamPagerBank = Team[7][:3]  #eg WWW
			TeamPager = Team[7][3:]      #eg 1

			if (TeamPagerBank != ''):
			 	print 'Page team = ' + TeamPagerBank + '-' + TeamPager
			else:
				print 'Team has no pager assigned to it'
				TeamPagerBank = 'WWW'
				TeamPager = '1'

			sql2 = "INSERT INTO Teams \
				(seq, Division, TeamNumber, UID, TeamPagerBank, TeamPager, Name) \
				VALUES \
				(%s, '%s', '%s', '%s', '%s', '%s', '%s')" % \
				(team_index, aDivision, TeamNum,aDivision+TeamNum, TeamPagerBank, TeamPager, TeamName )
			print sql2
			try:
				cursor2.execute(sql2)
				db2.commit()
				print "Insert OK"
			except:
				db2.rollback()
				print "Insert Error "
			team_index = team_index +1
		first_line = False
	db2.close()

#***************************************
#erases match datatable
#deletes the contents of the matches table and
#	resetes the index start
#***************************************
def init_match_db():
	db = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor = db.cursor()
	cursor.execute("DELETE FROM Matches")
	cursor.execute("ALTER TABLE Matches AUTO_INCREMENT = 0")
	db.close()
	print "DataBase initialised"

#***************************************
#erases ToPage datatable
#deletes the contents of the ToPage table and
#	resetes the index start
#***************************************
def init_topage_db():
	db = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor = db.cursor()
	cursor.execute("DELETE FROM ToPage")
	cursor.execute("ALTER TABLE ToPage AUTO_INCREMENT = 0")
	db.close()
	print "DataBase initialised"

#***************************************
#erases Teams datatable
#deletes the contents of the Teams table and
#	resetes the index start
#***************************************
def init_team_db():
	db = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor = db.cursor()
	cursor.execute("DELETE FROM Teams")
	cursor.execute("ALTER TABLE Teams AUTO_INCREMENT = 0")
	db.close()
	print "DataBase initialised"

#***************************************
#***************************************
def mark_page_as_done(aPage):
	print "Set pager msg as done"
	db2 = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor2 = db2.cursor()
	sql2 = "UPDATE ToPage SET done = 'Y' WHERE seq = %s" % (aPage)
	try:
		cursor2.execute(sql2)
		db2.commit()
		print "Update OK"
	except:
		db2.rollback()
		print "Update Error "
	db2.close()

#***************************************
#accesses the db that holds the teams to page
#***************************************
def read_need_to_page_db():
	db = MySQLdb.connect("localhost","pager","raspberry","VEX_TM")
	cursor = db.cursor()
	reset = False
	sql = "SELECT * \
		FROM ToPage \
		WHERE done = 'N'"
	try:
		cursor.execute(sql)
		results = cursor.fetchall()
		for row in results:
			seqNo = row[0]
			division = row[1]
			type = row[2]
			id = row[3]
			print "seqNo =%s, division=%s, type=%s, id=%s" % (seqNo, division, type, id)
			if (type == 'T'):
				print "Page Team"
				send_team_pager_msg(division,id)
			else:
			        if (type == 'X'):
					reset = True
				else:  
					print "Page Match"
					send_match_pages_msgs(division,id)
			#mark record as done
			mark_page_as_done(seqNo)
	except:
		print "No data"
	db.close()
	if (reset == True): #init the database
		init_match_db()
		init_team_db()
		init_topage_db()	
		add_all_matches_to_db()
		add_all_teams_to_db()

#***************************************
#***************************************
# now do the heavy lifting of the app
get_USB_Name()
load_pager_definitions()
print PagerID
print PagerCode

#add the matches to the database
test_db()
init_match_db()
init_team_db()
#init_topage_db()
add_all_matches_to_db()
add_all_teams_to_db()

while True:
	read_need_to_page_db()
	time.sleep(0.1)












