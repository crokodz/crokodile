#!/usr/bin/env python
import string, os, sys
reload(sys)
sys.setdefaultencoding("latin1")

import operator, string
from reportlab.platypus import *
from reportlab.lib.styles import PropertySet, getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.platypus.paragraph import Paragraph
from reportlab.platypus.flowables import PageBreak, Spacer
from reportlab.lib.units import inch, cm
from reportlab.lib.colors import red, black, navy, white, gray
from reportlab.lib.pagesizes import letter, portrait,landscape, A4,legal, legalz
import time, datetime
from types import TupleType, ListType, StringType

import codecs
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT, TA_JUSTIFY
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfgen.canvas import Canvas
from reportlab.lib import colors
from reportlab.lib.codecharts import KutenRowCodeChart, hBoxText
from reportlab.pdfbase.cidfonts import UnicodeCIDFont, findCMapFile
pdfmetrics.registerFont(UnicodeCIDFont('STSong-Light'))
from reportlab.lib.codecharts import Big5CodeChart, hBoxText
import locale


import MySQLdb

import ConfigParser

configFile = 'conf.ini'

parser = ConfigParser.ConfigParser()
parser.read( configFile )

dbhost = parser.get('dbparams','dbhost')
dbpass = parser.get('dbparams','dbpass')
dbuser = parser.get('dbparams','dbuser')
dbname = parser.get('dbparams','dbname')

mycon = MySQLdb.connect (host = dbhost, user = dbuser, passwd = dbpass, db = dbname)
cursor = mycon.cursor()

file = sys.argv[1]
id = sys.argv[2]
dtz = sys.argv[3]

select = """ SELECT title FROM posted_summary where posted_id = '%s' """ % (str(id))
cursor.execute(select)
post = cursor.fetchall()

dtz = str(post[0][0])


d=datetime.datetime.now()
todayz = d.strftime("%d %B %Y")

select = """ select `bank_account`,  `name`  from company join posted_summary on (company.id = posted_summary.company_id) where posted_summary.posted_id = '%s' """ % (str(id))
cursor.execute(select)
com = cursor.fetchall()

class PrintCustomer:
	def __init__(self):
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=1*inch, rightMargin=1*inch,
                                                bottomMargin=1*inch, topMargin=1*inch,pagesize = portrait(A4)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def myFirstPage(self, canvas, doc):
		stylesheet=getSampleStyleSheet()

		ts = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light')
			])

		canvas.saveState()

		canvas.setFont('Helvetica-Bold',8)
		canvas.drawString(2 * cm, 28 * cm, "%s" % str(todayz))

		text="""<para align=left><font size=11 face=Helvetica-Bold>BANK OF THE PHILIPPINE ISLANDS</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(300, 25)
		col1.drawOn(canvas, 2* cm, 26.2 * cm)

		text="""<para align=left><font size=11 face=Helvetica>G/F RUFFINO TOWER</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(300, 25)
		col1.drawOn(canvas, 2* cm, 25.7 * cm)

		text="""<para align=left><font size=11 face=Helvetica>AYALA AVENUE COR. V.A. RUFFINO</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(300, 25)
		col1.drawOn(canvas, 2* cm, 25.2 * cm)

		text="""<para align=left><font size=11 face=Helvetica>MAKATI CITY, PHILIPPINES</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(300, 25)
		col1.drawOn(canvas, 2* cm, 24.7 * cm)

		text="""<para align=center><font size=14 face=Helvetica-bOLD>A U T H O R I Z A T I O N</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(485, 25)
		col1.drawOn(canvas, 2* cm, 23.5 * cm)

		text="""<para align=left><font size=11 face=Helvetica>Please debit CURRENT ACCOUNT number <b>%s</b> of <b>%s</b>  and credit accounts of the following employees upon receipt hereof for the payroll pay date of <b>%s</b></font></para>""" % (str(com[0][0]),str(com[0][1]),str(dtz))
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(485, 25)
		col1.drawOn(canvas, 2* cm, 22 * cm)


		canvas.setFont('Times-Roman',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()

	def myLaterPage(self, canvas, doc):
		pass


	def getDataBlock(self, data):
		pass

	def RoundOff(self,num,places=2):
		if num==None:
			num = 0
		places = max(0,places)
		tmp = "%.*f" % (places, num)
		point = tmp.find(".")
		integer = (point == -1) and tmp or tmp[:point]
		decimal = (point != -1) and tmp[point:] or ""

		count =  0
		formatted = []
		for i in range(len(integer), 0, -1):
			count += 1
			formatted.append(integer[i - 1])
			if count % 3 == 0 and i - 1:
				formatted.append(",")

		integer = "".join(formatted[::-1])
		return integer+decimal

	def recheck(self, data):
		data = str(data)
		data = data.replace("&","&#38;")
		data = data.replace("<","&#60;")
		data = data.replace(">","&#62;")
		data = data.strip()
		return data

	def getMSB(self,ph):
		select = """ select `msb`, `ers`  from `ph` where `ees` =  '%s' """ % str(ph)
		cursor.execute(select)
		row = cursor.fetchall()
		if row:
			return [row[0][0],row[0][1]]
		else:
			return [0,0]

	def CreateContent(self):
		stylesheet=getSampleStyleSheet()
		ts = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light')
			])

		ts1 = TableStyle([
			('GRID', (0,0), (-1,-1), 0.25, colors.black),
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'Times-Roman')
			])

		ts2 = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light'),
			('BOTTOMPADDING', (0, 0), (-1, -1), 1.2)
			])

		self.__lst = []

		self.__lst.append(Spacer(0, 170))

		select = "select `fname`, `lname`, `bank_account`, `netpay` from `posted_summary` left join `employee` using (`em_id`) where `posted_id` = '%s' and `bank_account` != '' order by `lname` asc, `fname` asc" % str(id)
		cursor.execute(select)
		data = cursor.fetchall()

		text="""<para align=left leading=12.31><font size=11 face=Helvetica></font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])

		text="""<para align=center leading=12.31><font size=11 face=Helvetica-Bold>EMPLOYEE NAME</font></para>"""
		col2=Paragraph(text,stylesheet["BodyText"])

		text="""<para align=center leading=12.31><font size=11 face=Helvetica>BANK ACCT#</font></para>"""
		col3=Paragraph(text,stylesheet["BodyText"])


		text="""<para align=center leading=12.31><font size=11 face=Helvetica>AMOUNT</font></para>"""
		col4=Paragraph(text,stylesheet["BodyText"])



		t = Table([[col1,col2,col3,col4]], colWidths=[40,240,100,100])
		t.setStyle(ts1)
		self.__lst.append(t)

		total = 0

		for x in range(len(data)):
			print data[x]
			text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s.</font></para>""" % (str(x+1))
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s, %s</font></para>""" % (str(data[x][1]),str(data[x][0]))
			col2=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=center leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(data[x][2]))
			col3=Paragraph(text,stylesheet["BodyText"])


			text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(data[x][3])))
			col4=Paragraph(text,stylesheet["BodyText"])


			t = Table([[col1,col2,col3,col4]], colWidths=[40,240,100,100])
			t.setStyle(ts1)
			self.__lst.append(t)

			total = total + float(data[x][3])


		text="""<para align=right leading=12.31><font size=11 face=Helvetica>Grand Total</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])

		text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % str(str(self.RoundOff(total)))
		col2=Paragraph(text,stylesheet["BodyText"])

		t = Table([[col1,col2]], colWidths=[380,100])
		t.setStyle(ts1)
		self.__lst.append(t)


		self.__lst.append(Spacer(0, 20))

		text="""<para align=left leading=12.31><font size=11 face=Helvetica>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please make sure that no copy of this bank advise shall be given to any company personnel without the express permission of the undersigned.</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])

		t = Table([[col1]], colWidths=[480])
		t.setStyle(ts)
		self.__lst.append(t)


		self.__lst.append(Spacer(0, 40))


		text="""<para align=left leading=12.31><font size=11 face=Helvetica>MS JENNIFER C. VILLAR</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])

		text="""<para align=left leading=12.31><font size=11 face=Helvetica>ENRICO GUIDO CANOY</font></para>"""
		col2=Paragraph(text,stylesheet["BodyText"])

		t = Table([[col1,col2]], colWidths=[240,240])
		t.setStyle(ts)
		self.__lst.append(t)

		text="""<para align=left leading=12.31><font size=11 face=Helvetica>HR AND OD MANAGER</font></para>"""
		col1=Paragraph(text,stylesheet["BodyText"])

		text="""<para align=left leading=12.31><font size=11 face=Helvetica>VP FOR OPERATIONS</font></para>"""
		col2=Paragraph(text,stylesheet["BodyText"])

		t = Table([[col1,col2]], colWidths=[240,240])
		t.setStyle(ts)
		self.__lst.append(t)

		self.__lst.append(Spacer(0, 20))
		self.__lst.append(Spacer(0, 20))

		# text="""<para align=left leading=12.31><font size=11 face=Helvetica>Reviewed By:</font></para>"""
		# col1=Paragraph(text,stylesheet["BodyText"])

		# text="""<para align=left leading=12.31><font size=11 face=Helvetica></font></para>"""
		# col2=Paragraph(text,stylesheet["BodyText"])

		# t = Table([[col1,col2]], colWidths=[240,240])
		# t.setStyle(ts)
		# self.__lst.append(t)

		# text="""<para align=left leading=12.31><font size=11 face=Helvetica>GILMARK ANGELO AKYATAN</font></para>"""
		# col1=Paragraph(text,stylesheet["BodyText"])

		# text="""<para align=left leading=12.31><font size=11 face=Helvetica></font></para>"""
		# col2=Paragraph(text,stylesheet["BodyText"])

		# t = Table([[col1,col2]], colWidths=[240,240])
		# t.setStyle(ts)
		# self.__lst.append(t)

		select = "select `fname`, `lname`, `division`, `netpay` from `posted_summary` left join `employee` using (`em_id`) where `posted_id` = '%s' and `bank_account` = '' order by `lname` asc, `fname` asc" % str(id)
		cursor.execute(select)
		data = cursor.fetchall()

		if data:
			self.__lst.append(FrameBreak)


			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" % str(todayz)
			col1=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1]], colWidths=[480])
			t.setStyle(ts)
			self.__lst.append(t)

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>To : Accounting Department</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1]], colWidths=[480])
			t.setStyle(ts)
			self.__lst.append(t)\

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>From : HRDA-Payroll</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1]], colWidths=[480])
			t.setStyle(ts)
			self.__lst.append(t)


			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>Re :</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>Payroll Cheque (%s)</font></para>""" % str(dtz)
			col2=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1,col2]], colWidths=[40,440])
			t.setStyle(ts)
			self.__lst.append(t)

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold></font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" % str(com[0][1])
			col2=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1,col2]], colWidths=[40,440])
			t.setStyle(ts)
			self.__lst.append(t)

			self.__lst.append(Spacer(0, 40))


			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold></font></para>"""
			col0=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>Name</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>Division</font></para>"""
			col2=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>AMOUNT</font></para>"""
			col3=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col0,col1,col2,col3]], colWidths=[20,200,140,120])
			t.setStyle(ts)
			self.__lst.append(t)
			total = 0
			for x in range(len(data)):
				text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" %str(x+1)
				col0=Paragraph(text,stylesheet["BodyText"])

				text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>%s, %s</font></para>""" % (str(data[x][1]),str(data[x][0]))
				col1=Paragraph(text,stylesheet["BodyText"])

				text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" % (str(data[x][2]))
				col2=Paragraph(text,stylesheet["BodyText"])

				text="""<para align=right leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" % (str(self.RoundOff(data[x][3])))
				col3=Paragraph(text,stylesheet["BodyText"])

				t = Table([[col0,col1,col2,col3]], colWidths=[20,200,140,120])
				t.setStyle(ts1)
				self.__lst.append(t)

				total = total + float(data[x][3])

			self.__lst.append(Spacer(0, 80))

			text="""<para align=right leading=12.31><font size=11 face=Helvetica-Bold>Grand Total</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=right leading=12.31><font size=11 face=Helvetica-Bold>%s</font></para>""" % str(str(self.RoundOff(total)))
			col2=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1,col2]], colWidths=[360,120])
			t.setStyle(ts1)
			self.__lst.append(t)


			self.__lst.append(Spacer(0, 20))



			self.__lst.append(Spacer(0, 40))


			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>___________________________</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>___________________________</font></para>"""
			col2=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1,col2]], colWidths=[240,240])
			t.setStyle(ts)
			self.__lst.append(t)

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>prepared By</font></para>"""
			col1=Paragraph(text,stylesheet["BodyText"])

			text="""<para align=left leading=12.31><font size=11 face=Helvetica-Bold>Approved By</font></para>"""
			col2=Paragraph(text,stylesheet["BodyText"])

			t = Table([[col1,col2]], colWidths=[240,240])
			t.setStyle(ts)
			self.__lst.append(t)


        def CreateReport(self):
                self.__lst = []
                self.CreateContent()

if __name__=='__main__':
        PrintCustomer()
