import MySQLdb
from datetime import date
import time, datetime

today = date.today()
now = today.strftime("%Y%m%d")

mycon1 = MySQLdb.connect (host = '192.168.10.66', user = 'root', passwd = 'cm5r00tpa55wd.h3althway', db = 'payroll')
cursor1 = mycon1.cursor()

select = """ select `tin`, `em_id` from employee """
		
cursor1.execute(select)
data = cursor1.fetchall()
for x in range(len(data)):
	#~ update = """ update employee set `ts` = '%s', tin='YES' WHERE `em_id` = '%s' """ % (str(data[x][0]),str(data[x][1]))
	#~ cursor1.execute(update)