#!/usr/bin/python
# -*- coding: latin-1 -*-

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
import string, os, sys
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
fdate = sys.argv[2]
tdate = sys.argv[3]
compa = sys.argv[4]

select = """ select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '%s' """ % (str(compa))
cursor.execute(select)
com = cursor.fetchall()
	
class PrintCustomer:
	def __init__(self):
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=.5*inch, rightMargin=.5*inch,
                                                bottomMargin=1*inch, topMargin=2.46*inch,pagesize = landscape(legalz)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def myFirstPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('new.jpg', 30,50, width=880,height=523) 
		
		canvas.setFont('Helvetica-Bold',18)
		canvas.drawString(6* inch, 6.8 * inch, str(com[0][0]))
		
		canvas.setFont('Helvetica-Bold',14)
		canvas.drawString(10.4* inch, 6.75 * inch, str(com[0][2]))
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(2* inch, 6.45 * inch, str(com[0][1]))
		
		postcd = str(com[0][3])
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(11.4* inch, 6.40 * inch, str(postcd[0:1]))
		canvas.drawString(11.7* inch, 6.40 * inch, str(postcd[1:2]))
		canvas.drawString(12* inch, 6.40 * inch, str(postcd[2:3]))
		canvas.drawString(12.3* inch, 6.40 * inch, str(postcd[3:4]))
		
		tin = str(com[0][4])
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(.55* inch, 6.75 * inch, str(tin[0:1]))
		canvas.drawString(.73* inch, 6.75 * inch, str(tin[1:2]))
		canvas.drawString(.91* inch, 6.75 * inch, str(tin[2:3]))
		canvas.drawString(1.09* inch, 6.75 * inch, str(tin[3:4]))
		canvas.drawString(1.27* inch, 6.75 * inch, str(tin[4:5]))
		canvas.drawString(1.45* inch, 6.75 * inch, str(tin[5:6]))
		canvas.drawString(1.63* inch, 6.75 * inch, str(tin[6:7]))
		canvas.drawString(1.81* inch, 6.75 * inch, str(tin[7:8]))
		canvas.drawString(1.99* inch, 6.75 * inch, str(tin[8:9]))
		canvas.drawString(2.17* inch, 6.75 * inch, str(tin[9:10]))
		canvas.drawString(2.35* inch, 6.75 * inch, str(tin[10:11]))
		canvas.drawString(2.53* inch, 6.75 * inch, str(tin[11:12]))
		
		
		#canvas.drawString(29.5* cm, 2.1 * cm, str(doc.page))
				
		canvas.setFont('Helvetica',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()
			
	def myLaterPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('new.jpg', 30,50, width=880,height=523) 
		
		canvas.setFont('Helvetica-Bold',18)
		canvas.drawString(6* inch, 6.8 * inch, str(com[0][0]))
		
		canvas.setFont('Helvetica-Bold',14)
		canvas.drawString(10.4* inch, 6.75 * inch, str(com[0][2]))
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(2* inch, 6.45 * inch, str(com[0][1]))
		
		postcd = str(com[0][3])
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(11.4* inch, 6.40 * inch, str(postcd[0:1]))
		canvas.drawString(11.7* inch, 6.40 * inch, str(postcd[1:2]))
		canvas.drawString(12* inch, 6.40 * inch, str(postcd[2:3]))
		canvas.drawString(12.3* inch, 6.40 * inch, str(postcd[3:4]))
		
		tin = str(com[0][4])
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(.55* inch, 6.75 * inch, str(tin[0:1]))
		canvas.drawString(.73* inch, 6.75 * inch, str(tin[1:2]))
		canvas.drawString(.91* inch, 6.75 * inch, str(tin[2:3]))
		canvas.drawString(1.09* inch, 6.75 * inch, str(tin[3:4]))
		canvas.drawString(1.27* inch, 6.75 * inch, str(tin[4:5]))
		canvas.drawString(1.45* inch, 6.75 * inch, str(tin[5:6]))
		canvas.drawString(1.63* inch, 6.75 * inch, str(tin[6:7]))
		canvas.drawString(1.81* inch, 6.75 * inch, str(tin[7:8]))
		canvas.drawString(1.99* inch, 6.75 * inch, str(tin[8:9]))
		canvas.drawString(2.17* inch, 6.75 * inch, str(tin[9:10]))
		canvas.drawString(2.35* inch, 6.75 * inch, str(tin[10:11]))
		canvas.drawString(2.53* inch, 6.75 * inch, str(tin[11:12]))
		
				
		canvas.setFont('Helvetica',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()

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
		
	def getsal(self, salary):
		select = "select `msc` from sss where `to` >= '%s' order by `from` asc limit 1" %(str(salary))
		cursor.execute(select)
		sss = cursor.fetchall()
		if sss:
			return float(sss[0][0])
		else:
			return 0

	def CreateContent(self):
		stylesheet=getSampleStyleSheet()
		ts = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light')
			])
		
		ts1 = TableStyle([
			('GRID', (0,0), (-1,-1), 0.25, colors.blue),
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'Times-Roman')
			])
			
		ts2 = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light'),
			('BOTTOMPADDING', (0, 0), (-1, -1), 1.2)
			])
			
		self.__lst = []
		
		select = "select `sssn`, `lname`, `fname`, `mname`, `birthdate`, `position`, `salary`, `date_employed` from employee where `date_employed` between '%s' and '%s' and `company_id` = '%s' and `sss` != 'NO' order by lname asc, fname asc, mname asc" % (str(fdate),str(tdate),str(compa))
		cursor.execute(select)
		row = cursor.fetchall()
		z = 0
		for x in range(len(row)):
			s=str(row[x][0]).replace("-","")
			name = str(row[x][1]).strip() + ", " + str(row[x][2]).strip() + " " + (str(row[x][3]).strip())[0:1] + "."
			bd = str(row[x][4]).replace("-","")
			sal = self.getsal(row[x][6])
			sal =  "@" + str(self.RoundOff(sal,0)).replace(",","")
			sal = sal.replace("","&nbsp;&nbsp;")
			sal = sal.replace("&nbsp;&nbsp;@&nbsp;&nbsp;","")
			de = str(row[x][7]).replace("-","")
			
			
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s</font></para>""" % (s[0:1],s[1:2],s[2:3],s[3:4],s[4:5],s[5:6],s[6:7],s[7:8],s[8:9],s[9:10])
			col1=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>&nbsp;&nbsp;&nbsp;%s</font></para>""" % name
			col2=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s</font></para>""" % (bd[4:5],bd[5:6],bd[6:7],bd[7:8],bd[0:1],bd[1:2],bd[2:3],bd[3:4])
			col3=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s...</font></para>""" % str(row[x][5])[0:16]
			col4=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s</font></para>""" % (sal)
			col5=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s&nbsp;&nbsp;%s</font></para>""" % (de[4:5],de[5:6],de[6:7],de[7:8],de[0:1],de[1:2],de[2:3],de[3:4])
			col6=Paragraph(text,stylesheet["BodyText"])
	
				
			t = Table([[col1,col2,col3,col4,col5,col6]], colWidths=[125,232,108,140,70,195])
			t.setStyle(ts)
			self.__lst.append(t)
			
			z = z + 1
			if z ==20:
				z = 0
				self.__lst.append(Spacer(0, 10))
				text="""<para align=left leading=7.85><font size=11 face=Helvetica>20</font></para>"""
				col1=Paragraph(text,stylesheet["BodyText"])
				text="""<para align=left leading=7.85><font size=11 face=Helvetica></font></para>"""
				col2=Paragraph(text,stylesheet["BodyText"])
				
				t = Table([[col1,col2]], colWidths=[68,615])
				t.setStyle(ts)
				self.__lst.append(t)
				
				self.__lst.append(FrameBreak)
				
		if z > 0:
			for d in range(20 - z):
				text="""<para align=left leading=7.85><font size=11 face=Helvetica>&nbsp;</font></para>"""
				col1=Paragraph(text,stylesheet["BodyText"])
				t = Table([[col1]], colWidths=[68])
				t.setStyle(ts)
				self.__lst.append(t)
				
			self.__lst.append(Spacer(0, 10))
			text="""<para align=left leading=7.85><font size=11 face=Helvetica>%s</font></para>""" % str(z)
			col1=Paragraph(text,stylesheet["BodyText"])
			text="""<para align=left leading=7.85><font size=11 face=Helvetica></font></para>"""
			col2=Paragraph(text,stylesheet["BodyText"])
				
			t = Table([[col1,col2]], colWidths=[68,615])
			t.setStyle(ts)
			self.__lst.append(t)
			
		###########
		
		self.__lst.append(FrameBreak)

        def CreateReport(self):
                self.__lst = []
                self.CreateContent()

if __name__=='__main__':
        PrintCustomer()