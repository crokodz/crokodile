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
vars = sys.argv[2]
compa = sys.argv[3]
td = str(sys.argv[4]).replace("@@"," ")
ap = str(sys.argv[5]).replace("@@"," ")[0:9]
ors = str(sys.argv[6]).replace("@@"," ")



select = """ select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '%s' """ % (str(compa))
cursor.execute(select)
com = cursor.fetchall()

phn = (com[0][8]).replace("-","")
print phn
tin = (com[0][2]).replace("-","")
sss = (com[0][4]).replace("-","")

appendsql = " ( "
var = vars.split("@@")
for y in range(len(var)-1):
	if var[y]:
		if y==len(var)-2:
			appendsql = appendsql + " tb1.`posted_id` = '%s') " % var[y]
		else:
			appendsql = appendsql + " tb1.`posted_id` = '%s' or " % var[y]

class PrintCustomer:
	def __init__(self):
		
		z=0
		select = """ select tb2.`fname`, tb2.`lname`, tb2.`mname`, tb2.`phn`, sum(tb1.`ph`), tb1.`payday`, tb2.`sssn`
			from `posted_summary` tb1 left join employee tb2 using(`em_id`) where %s group by `em_id` order by lname asc, fname asc, mname asc""" % str(appendsql)
		cursor.execute(select)
		rowxx = cursor.fetchall()
		self.rowxx=rowxx
		self.err = []
		self.ess = []
		self.toterr = 0
		self.totess = 0
		err = 0
		ess = 0
		self.totalem = 0
		self.total = 0
		self.page = 0
		for x in range(len(rowxx)):
			if x==0:
				dt = (rowxx[x][5]).split("-")
				yy = (dt[0]).split("@")
				self.yy = (yy[1])[2:4]
				self.mm = self.getM(dt[1])
				
			if float(rowxx[x][4]):
				ph = self.getMSB(rowxx[x][4])
				z = z + 1
				ess = ess + float(rowxx[x][4])
				self.totess = self.totess + float(rowxx[x][4])
				self.toterr = self.toterr + float(ph[1])
				err = err + float(ph[1])
				self.totalem = self.totalem + 1
				self.total = self.total + float(ph[1]) + float(rowxx[x][4])
				if z ==15:
					self.page = self.page + 1
					self.ess.append(ess)
					self.err.append(err)
					err = 0
					ess = 0
					z = 0
					
		if z > 0:
			#~ ess = ess + float(rowxx[x][4])
			#~ err = err + float(ph[1])
			#~ self.totalem = self.totalem + 1
			self.total = self.total + float(ph[1]) + float(rowxx[x][4])
			self.page = self.page + 1
			self.ess.append(ess)
			self.err.append(err)
			
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=.5*inch, rightMargin=.5*inch,
                                                bottomMargin=.5*inch, topMargin=2.73*inch,pagesize = landscape(legalz)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def getM(self,dt):
		a = ["","January","February","March","April","May","June","July","August","September","October","November","December"]
		return a[int(dt)]

	def myFirstPage(self, canvas, doc):
		stylesheet=getSampleStyleSheet()
		
		ts = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light')
			])
		
		canvas.saveState()
		canvas.drawInlineImage('ph_remit.jpg', 30,50, width=880,height=554) 
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(5.1* cm, 18.94 * cm, phn[0])
		canvas.drawString(5.65* cm, 18.94 * cm, phn[1])
		canvas.drawString(6.47* cm, 18.94 * cm, phn[2])
		canvas.drawString(7.02* cm, 18.94 * cm, phn[3])
		canvas.drawString(7.57* cm, 18.94 * cm, phn[4])
		canvas.drawString(8.12* cm, 18.94 * cm, phn[5])
		canvas.drawString(8.67* cm, 18.94 * cm, phn[6])
		canvas.drawString(9.18* cm, 18.94 * cm, phn[7])
		canvas.drawString(9.73* cm, 18.94 * cm, phn[8])
		canvas.drawString(10.25* cm, 18.94 * cm, phn[9])
		canvas.drawString(10.78* cm, 18.94 * cm, phn[10])
		canvas.drawString(11.6* cm, 18.94 * cm, phn[11])
		
		canvas.drawString(5.1* cm, 18.3 * cm, tin[0])
		canvas.drawString(5.65* cm, 18.3 * cm, tin[1])
		canvas.drawString(6.2* cm, 18.3 * cm, tin[2])
		canvas.drawString(7.02* cm, 18.3 * cm, tin[3])
		canvas.drawString(7.57* cm, 18.3 * cm, tin[4])
		canvas.drawString(8.12* cm, 18.3 * cm, tin[5])
		canvas.drawString(8.78* cm, 18.3 * cm, tin[6])
		canvas.drawString(9.33* cm, 18.3 * cm, tin[7])
		canvas.drawString(9.9* cm, 18.3 * cm, tin[8])
					
		canvas.setFont('Helvetica-Bold',9)
		canvas.drawString(6.2* cm, 17.7 * cm, str(com[0][0]))
		canvas.drawString(6.2* cm, 17.26 * cm, str(com[0][1]))
		canvas.drawString(4.5* cm, 16.34 * cm, str(com[0][5]))
		
		canvas.drawString(15.33* cm, 17.18 * cm, 'X')
		canvas.drawString(22.67* cm, 17.18 * cm, 'X')
		canvas.drawString(28.2* cm, 16.9 * cm, self.mm)
		canvas.drawString(31* cm, 16.9 * cm, self.yy)
		
		canvas.drawString(29.5* cm, 2.1 * cm, str(doc.page))
		
		text="""<para align=center><font size=8 face=Helvetica>%s</font></para>""" % str(com[0][7])
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 28.07* cm, 3.25 * cm)
		
		text="""<para align=center><font size=8 face=Helvetica>%s</font></para>""" % str(com[0][6])
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 28.07* cm, 3.85 * cm)
		
		text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.ess[int(doc.page)-1]))
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 21.75* cm, 4.15 * cm)
		
		text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.err[int(doc.page)-1]))
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 24.2* cm, 4.15 * cm)
		
		canvas.drawString(15.33* cm, 17.18 * cm, 'X')
		
		if self.page == doc.page:
		
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(ap)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(70, 25)
			col1.drawOn(canvas, 1.6* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.total))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(68, 25)
			col1.drawOn(canvas, 4.3* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(ors)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(80, 25)
			col1.drawOn(canvas, 6.95* cm, 2.85 * cm)
			
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(td)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(60, 25)
			col1.drawOn(canvas, 10.05* cm, 2.85 * cm)
			
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(self.totalem)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(60, 25)
			col1.drawOn(canvas, 12.3* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.toterr))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 24.2* cm, 3.15 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.totess))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 21.7* cm, 3.15 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.total))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 24.2* cm, 2.6 * cm)
			
					
		canvas.setFont('Times-Roman',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()
			
	def myLaterPage(self, canvas, doc):
		stylesheet=getSampleStyleSheet()
		
		ts = TableStyle([
			('VALIGN',(0,0),(-1,-1),'TOP'),
			('FONT', (0,0), (-1,-1), 'STSong-Light')
			])
		
		canvas.saveState()
		canvas.drawInlineImage('ph_remit.jpg', 30,50, width=880,height=554) 
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(5.1* cm, 18.94 * cm, phn[0])
		canvas.drawString(5.65* cm, 18.94 * cm, phn[1])
		canvas.drawString(6.47* cm, 18.94 * cm, phn[2])
		canvas.drawString(7.02* cm, 18.94 * cm, phn[3])
		canvas.drawString(7.57* cm, 18.94 * cm, phn[4])
		canvas.drawString(8.12* cm, 18.94 * cm, phn[5])
		canvas.drawString(8.67* cm, 18.94 * cm, phn[6])
		canvas.drawString(9.18* cm, 18.94 * cm, phn[7])
		canvas.drawString(9.73* cm, 18.94 * cm, phn[8])
		canvas.drawString(10.25* cm, 18.94 * cm, phn[9])
		canvas.drawString(10.78* cm, 18.94 * cm, phn[10])
		canvas.drawString(11.6* cm, 18.94 * cm, phn[11])
		
		canvas.drawString(5.1* cm, 18.3 * cm, tin[0])
		canvas.drawString(5.65* cm, 18.3 * cm, tin[1])
		canvas.drawString(6.2* cm, 18.3 * cm, tin[2])
		canvas.drawString(7.02* cm, 18.3 * cm, tin[3])
		canvas.drawString(7.57* cm, 18.3 * cm, tin[4])
		canvas.drawString(8.12* cm, 18.3 * cm, tin[5])
		canvas.drawString(8.78* cm, 18.3 * cm, tin[6])
		canvas.drawString(9.33* cm, 18.3 * cm, tin[7])
		canvas.drawString(9.9* cm, 18.3 * cm, tin[8])
					
		canvas.setFont('Helvetica-Bold',9)
		canvas.drawString(6.2* cm, 17.7 * cm, str(com[0][0]))
		canvas.drawString(6.2* cm, 17.26 * cm, str(com[0][1]))
		canvas.drawString(4.5* cm, 16.34 * cm, str(com[0][5]))
		
		canvas.drawString(15.33* cm, 17.18 * cm, 'X')
		canvas.drawString(22.67* cm, 17.18 * cm, 'X')
		canvas.drawString(28.2* cm, 16.9 * cm, self.mm)
		canvas.drawString(31* cm, 16.9 * cm, self.yy)
		
		canvas.drawString(29.5* cm, 2.1 * cm, str(doc.page))
		
		text="""<para align=center><font size=8 face=Helvetica>%s</font></para>""" % str(com[0][7])
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 28.07* cm, 3.25 * cm)
		
		text="""<para align=center><font size=8 face=Helvetica>%s</font></para>""" % str(com[0][6])
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 28.07* cm, 3.85 * cm)
		
		text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.ess[int(doc.page)-1]))
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 21.75* cm, 4.15 * cm)
		
		text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.err[int(doc.page)-1]))
		col1=Paragraph(text,stylesheet["BodyText"])
		w, h = col1.wrap(100, 25)
		col1.drawOn(canvas, 24.2* cm, 4.15 * cm)
		
		if self.page == doc.page:
		
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(ap)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(70, 25)
			col1.drawOn(canvas, 1.6* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.totess + self.toterr))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(68, 25)
			col1.drawOn(canvas, 4.3* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(ors)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(80, 25)
			col1.drawOn(canvas, 6.95* cm, 2.85 * cm)
			
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(td)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(60, 25)
			col1.drawOn(canvas, 10.05* cm, 2.85 * cm)
			
			text="""<para align=left><font size=11 face=Helvetica>%s</font></para>""" % str(self.totalem)
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(60, 25)
			col1.drawOn(canvas, 12.3* cm, 2.85 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.toterr))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 24.2* cm, 3.15 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.totess))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 21.7* cm, 3.15 * cm)
			
			text="""<para align=right><font size=11 face=Helvetica>%s</font></para>""" % str(self.RoundOff(self.totess + self.toterr))
			col1=Paragraph(text,stylesheet["BodyText"])
			w, h = col1.wrap(100, 25)
			col1.drawOn(canvas, 24.2* cm, 2.6 * cm)
			
					
		canvas.setFont('Times-Roman',7)
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
		z = 0
		
		row = self.rowxx
		
		for x in range(len(row)):
			if float(row[x][4]):
				ph = self.getMSB(row[x][4])
				
				phn = (row[x][3]).replace("-","")
				phn = (phn).replace(" ","")
				phn = (phn).replace(".","")
				phn = phn + '0000000000000'
				
				text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(row[x][1]).strip())
				col1=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % ((str(row[x][0]).strip())[0:15])
				col2=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(row[x][2]).strip())
				col3=Paragraph(text,stylesheet["BodyText"])
				
				if int(phn) == 0:
					if row[x][6]:
						try:
							phn = (row[x][6]).replace("-","")
							text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s</font></para>""" % (phn[0],phn[1],phn[2],phn[3],phn[4],phn[5],phn[6],phn[7],phn[8],phn[9])
							col4=Paragraph(text,stylesheet["BodyText"])
						except:
							phn = (row[x][6]).replace("-","")
							if len(phn) < 10:
								phn = (row[x][6]+"      ").replace("-","")
							text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s</font></para>""" % (phn[0],phn[1],phn[2],phn[3],phn[4],phn[5],phn[6],phn[7],phn[8],phn[9])
							col4=Paragraph(text,stylesheet["BodyText"])
					else:
						pass
				else:
					text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s</font></para>""" % (phn[0],phn[1],phn[2],phn[3],phn[4],phn[5],phn[6],phn[7],phn[8],phn[9],phn[10],phn[11])
					col4=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(ph[0],0)).strip())
				col5=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(row[x][4])).strip())
				col6=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=right leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(ph[1])).strip())
				col7=Paragraph(text,stylesheet["BodyText"])
				
				text="""<para align=left leading=12.31><font size=11 face=Helvetica>%s</font></para>""" % (str('').strip())
				col8=Paragraph(text,stylesheet["BodyText"])
				
				t = Table([[col1,col2,col3,col4,col5,col6,col7,col8]], colWidths=[113,120,115,180,50,80,70,80])
				t.setStyle(ts)
				self.__lst.append(t)
				
				z = z + 1
				if z ==15:
					z = 0
					self.__lst.append(FrameBreak)

        def CreateReport(self):
                self.__lst = []
                self.CreateContent()

if __name__=='__main__':
        PrintCustomer()