import time, os, sys
import ConfigParser
import MySQLdb

configFile = 'conf.ini'

parser = ConfigParser.ConfigParser()
parser.read( configFile )

dbhost = parser.get('dbparams','dbhost')
dbpass = parser.get('dbparams','dbpass')
dbuser = parser.get('dbparams','dbuser')
dbname = parser.get('dbparams','dbname')


def conz():
	mycon = MySQLdb.connect (host = dbhost, user = dbuser, passwd = dbpass, db = dbname)
	return mycon.cursor()


cursor = conz()
cursor.execute(""" select em_id from employee """)
data = cursor.fetchall()


for x in range(len(data)):
	insert = """ insert into user_privileges (`username`, `menu`) values('%s', '10') """ % str(data[x][0])
	cursor.execute(insert)
	insert = """ insert into user_privileges (`username`, `menu`) values('%s', '2') """ % str(data[x][0])
	cursor.execute(insert)
	insert = """ insert into user_privileges (`username`, `menu`) values('%s', '11') """ % str(data[x][0])
	cursor.execute(insert)
	print x
cursor.close()