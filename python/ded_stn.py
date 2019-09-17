import time
import datetime
import MySQLdb


d = time.localtime(time.time())
date = time.strftime("%Y-%m-%d", d)
date="2017-01-04"

dd = date.split("-")
ddd = datetime.date(int(dd[0]), int(dd[1]), int(dd[2]))
dname = ddd.strftime("%a")

mycon = MySQLdb.connect (host = "localhost", user = "root", passwd = "", db = "payroll")
cursor = mycon.cursor()

select = """ select `EMP CODE`, master_ded_stn.* from master_ded_stn """
cursor.execute(select)
em = cursor.fetchall()
for x in range(len(em)):
	#sss

	if em[x][3] > 0:
		bal = float(em[x][4])
		select1 = """ select max(sub_id)+1 as sub_id from employee_deduction """
		cursor.execute(select1)
		em1 = cursor.fetchall()
		sub_id = em1[0][0]
		while bal >= em[x][3]:
			bal = bal - em[x][3]
			insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'SSS LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(em[x][3]), str(em[x][4]), str(em[x][4]), str(em[x][3]), str(em[x][4]))
			cursor.execute(insert)
		#time.sleep(2)
		insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'SSS LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(bal), str(em[x][4]), str(em[x][4]), str(bal), str(em[x][4]))
		cursor.execute(insert)

	#cru
	if em[x][5] > 0:
		bal = float(em[x][6])
		select1 = """ select max(sub_id)+1 as sub_id from employee_deduction """
		cursor.execute(select1)
		em1 = cursor.fetchall()
		sub_id = em1[0][0]
		while bal >= em[x][5]:
			bal = bal - em[x][5]
			insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'PAG-IBIG LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(em[x][5]), str(em[x][6]), str(em[x][6]), str(em[x][5]), str(em[x][6]))
			cursor.execute(insert)
		#time.sleep(2)
		insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'PAG-IBIG LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(bal), str(em[x][6]), str(em[x][6]), str(bal), str(em[x][6]))
		cursor.execute(insert)



	#cash


	if em[x][7] > 0:
		bal = float(em[x][8])
		select1 = """ select max(sub_id)+1 as sub_id from employee_deduction """
		cursor.execute(select1)
		em1 = cursor.fetchall()
		sub_id = em1[0][0]
		while bal >= em[x][7]:
			print em[x][7]
			bal = bal - float(em[x][7])
			insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'CALAMITY LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(em[x][7]), str(em[x][8]), str(em[x][8]), str(em[x][7]), str(em[x][8]))
			cursor.execute(insert)
		#time.sleep(2)
		insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'CALAMITY LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(bal), str(em[x][8]), str(em[x][8]), str(bal), str(em[x][8]))
		cursor.execute(insert)



	#pi
	if em[x][9] > 0:
		bal = float(em[x][10])
		select1 = """ select max(sub_id)+1 as sub_id from employee_deduction """
		cursor.execute(select1)
		em1 = cursor.fetchall()
		sub_id = em1[0][0]
		while bal >= em[x][9]:
			bal = bal - em[x][9]
			insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'CRU LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(em[x][9]), str(em[x][10]), str(em[x][10]), str(em[x][9]), str(em[x][10]))
			cursor.execute(insert)
		#time.sleep(2)
		insert = """ insert into employee_deduction (
					sub_id,
					em_id,
					name,
					amount,
					status,
					date,
					username,
					datetime,
					date_granted,
					date_effectivity,
					principal_amount,
					gross_amount,
					ded_amount,
					granted_amount) values (
					'%s', '%s', 'CRU LOAN', '%s', 'pending', curdate(), 'admin1', now(), curdate(), curdate(), '%s', '%s', '%s', '%s'
					)""" % (sub_id, str(em[x][0]), str(bal), str(em[x][10]), str(em[x][10]), str(bal), str(em[x][10]))
		cursor.execute(insert)
