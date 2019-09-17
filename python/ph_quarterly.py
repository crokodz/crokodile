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

select = """ select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '%s' """ % (str(compa))
cursor.execute(select)
com = cursor.fetchall()

phn = (com[0][8]).replace("-","")
tin = (com[0][2]).replace("-","")
sss = (com[0][4]).replace("-","")

var = vars.split("@@")
	
class PrintCustomer:
	def __init__(self):
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=.5*inch, rightMargin=.5*inch,
                                                bottomMargin=.5*inch, topMargin=3*inch,pagesize = landscape(legalz)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def myFirstPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('ph_quarterly.jpg', 30,50, width=890,height=548) 
		
		canvas.setFont('Helvetica-Bold',10)
		canvas.drawString(5.97* cm, 18.75 * cm, phn[0])
		canvas.drawString(6.4* cm, 18.75 * cm, phn[1])
		canvas.drawString(7.1* cm, 18.75 * cm, phn[2])
		canvas.drawString(7.53* cm, 18.75 * cm, phn[3])
		canvas.drawString(8* cm, 18.75 * cm, phn[4])
		canvas.drawString(8.45* cm, 18.75 * cm, phn[5])
		canvas.drawString(8.9* cm, 18.75 * cm, phn[6])
		canvas.drawString(9.37* cm, 18.75 * cm, phn[7])
		canvas.drawString(9.79* cm, 18.75 * cm, phn[8])
		canvas.drawString(10.27* cm, 18.75 * cm, phn[9])
		canvas.drawString(10.72* cm, 18.75 * cm, phn[10])
		canvas.drawString(11.42* cm, 18.75 * cm, phn[11])
		
		canvas.drawString(5.87* cm, 18.2 * cm, tin[0])
		canvas.drawString(6.32* cm, 18.2 * cm, tin[1])
		canvas.drawString(6.77* cm, 18.2 * cm, tin[2])
		canvas.drawString(7.45* cm, 18.2 * cm, tin[3])
		canvas.drawString(7.9* cm, 18.2 * cm, tin[4])
		canvas.drawString(8.35* cm, 18.2 * cm, tin[5])
		canvas.drawString(9.03* cm, 18.2 * cm, tin[6])
		canvas.drawString(9.48* cm, 18.2 * cm, tin[7])
		canvas.drawString(9.93* cm, 18.2 * cm, tin[8])
		#~ canvas.drawString(10.61* cm, 18.2 * cm, '0')
		#~ canvas.drawString(11.06* cm, 18.2 * cm, '0')
		#~ canvas.drawString(11.51* cm, 18.2 * cm, '0')
		
		canvas.drawString(17.37* cm, 16.69 * cm, sss[0])
		canvas.drawString(17.82* cm, 16.69 * cm, sss[1])
		canvas.drawString(18.5* cm, 16.69 * cm, sss[2])
		canvas.drawString(18.95* cm, 16.69 * cm, sss[3])
		canvas.drawString(19.4* cm, 16.69 * cm, sss[4])
		canvas.drawString(19.85* cm, 16.69 * cm, sss[5])
		canvas.drawString(20.3* cm, 16.69 * cm, sss[6])
		canvas.drawString(20.75* cm, 16.69 * cm, sss[7])
		canvas.drawString(21.2* cm, 16.69 * cm, sss[8])
		canvas.drawString(21.88* cm, 16.69 * cm, sss[9])
		
				
		canvas.setFont('Helvetica-Bold',9)
		canvas.drawString(6.9* cm, 17.18 * cm, str(com[0][0]))
		canvas.drawString(7* cm, 16.74 * cm, str(com[0][1]))
		canvas.drawString(7* cm, 16.34 * cm, str(com[0][5]))
		
		canvas.drawString(26.8* cm, 4.78 * cm, str(com[0][6]))
		canvas.drawString(27.7* cm, 3.95 * cm, str(com[0][7]))
		
		canvas.drawString(4* cm, 4 * cm, str('0.00'))
		
		
		
		canvas.drawString(14.27* cm, 17.18 * cm, 'X')
		canvas.drawString(14.87* cm, 16.796 * cm, 'X')
		canvas.drawString(22.67* cm, 17.18 * cm, 'X')
				
		canvas.setFont('Times-Roman',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()
		
		
		#~ text="""<para align=right><font size=9 face=wtLight>%s醫生</font></para>""" % str(doctor)
		#~ col1=Paragraph(text,stylesheet["BodyText"])
		#~ w, h = col1.wrap(100, 25)
		#~ col1.drawOn(canvas, 1.53* inch, 1.68* inch)
			
	def myLaterPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('ph_quarterly.jpg', 30,50, width=890,height=548) 
		
		canvas.setFont('Helvetica-Bold',10)
		canvas.drawString(5.97* cm, 18.75 * cm, phn[0])
		canvas.drawString(6.4* cm, 18.75 * cm, phn[1])
		canvas.drawString(7.1* cm, 18.75 * cm, phn[2])
		canvas.drawString(7.53* cm, 18.75 * cm, phn[3])
		canvas.drawString(8* cm, 18.75 * cm, phn[4])
		canvas.drawString(8.45* cm, 18.75 * cm, phn[5])
		canvas.drawString(8.9* cm, 18.75 * cm, phn[6])
		canvas.drawString(9.37* cm, 18.75 * cm, phn[7])
		canvas.drawString(9.79* cm, 18.75 * cm, phn[8])
		canvas.drawString(10.27* cm, 18.75 * cm, phn[9])
		canvas.drawString(10.72* cm, 18.75 * cm, phn[10])
		canvas.drawString(11.42* cm, 18.75 * cm, phn[11])
		
		canvas.drawString(5.87* cm, 18.2 * cm, tin[0])
		canvas.drawString(6.32* cm, 18.2 * cm, tin[1])
		canvas.drawString(6.77* cm, 18.2 * cm, tin[2])
		canvas.drawString(7.45* cm, 18.2 * cm, tin[3])
		canvas.drawString(7.9* cm, 18.2 * cm, tin[4])
		canvas.drawString(8.35* cm, 18.2 * cm, tin[5])
		canvas.drawString(9.03* cm, 18.2 * cm, tin[6])
		canvas.drawString(9.48* cm, 18.2 * cm, tin[7])
		canvas.drawString(9.93* cm, 18.2 * cm, tin[8])
		#~ canvas.drawString(10.61* cm, 18.2 * cm, '0')
		#~ canvas.drawString(11.06* cm, 18.2 * cm, '0')
		#~ canvas.drawString(11.51* cm, 18.2 * cm, '0')
		
		canvas.drawString(17.37* cm, 16.69 * cm, sss[0])
		canvas.drawString(17.82* cm, 16.69 * cm, sss[1])
		canvas.drawString(18.5* cm, 16.69 * cm, sss[2])
		canvas.drawString(18.95* cm, 16.69 * cm, sss[3])
		canvas.drawString(19.4* cm, 16.69 * cm, sss[4])
		canvas.drawString(19.85* cm, 16.69 * cm, sss[5])
		canvas.drawString(20.3* cm, 16.69 * cm, sss[6])
		canvas.drawString(20.75* cm, 16.69 * cm, sss[7])
		canvas.drawString(21.2* cm, 16.69 * cm, sss[8])
		canvas.drawString(21.88* cm, 16.69 * cm, sss[9])
		
				
		canvas.setFont('Helvetica-Bold',9)
		canvas.drawString(6.9* cm, 17.18 * cm, str(com[0][0]))
		canvas.drawString(7* cm, 16.74 * cm, str(com[0][1]))
		canvas.drawString(7* cm, 16.34 * cm, str(com[0][5]))
		
		canvas.drawString(26.8* cm, 4.78 * cm, str(com[0][6]))
		canvas.drawString(27.7* cm, 3.95 * cm, str(com[0][7]))
		
		canvas.drawString(4* cm, 4 * cm, str('0.00'))
		
		
		
		canvas.drawString(14.27* cm, 17.18 * cm, 'X')
		canvas.drawString(14.87* cm, 16.796 * cm, 'X')
		canvas.drawString(22.67* cm, 17.18 * cm, 'X')
				
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
		
		for y in range(len(var)):
			if var[y]:
				select = """ select tb2.`fname`, tb2.`lname`, tb2.`mname`, tb2.`phn`, tb1.`ph`
					from `posted_summary` tb1 left join employee tb2 using(`em_id`) where tb1.`posted_id` = '%s' """ % str(var[y])
				cursor.execute(select)
				row = cursor.fetchall()
				
				
				for x in range(len(row)):
					if row[x][2]:
						mname = (str(row[x][2]).strip())[0]
					else:
						mname = ""
						
					ph = self.getMSB(row[x][4])
						
					text="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(row[x][1]).strip())
					col1=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(row[x][0]).strip())
					col2=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(mname))
					col3=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(row[x][3]).strip())
					col4=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(ph[0],0)).strip())
					col5=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(row[x][4])).strip())
					col6=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(ph[1])).strip())
					col7=Paragraph(text,stylesheet["BodyText"])
					
					text="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str('').strip())
					col8=Paragraph(text,stylesheet["BodyText"])
					
					t = Table([[col1,col2,col3,col4,col5,col6,col7,col8]], colWidths=[108,125,15,77,85,140,143,152])
					t.setStyle(ts)
					self.__lst.append(t)
					
					z = z + 1
					if z ==18:
						z = 1
						
						self.__lst.append(Spacer(0, 15))
						
						textb="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str("").strip())
						col1=Paragraph(textb,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col6=Paragraph(text,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col7=Paragraph(text,stylesheet["BodyText"])
						
						t = Table([[col1,col1,col1,col1,col1,col6,col7,col1]], colWidths=[108,125,15,77,85,140,143,152])
						t.setStyle(ts)
						self.__lst.append(t)
												
						#-----------------------------------------
						self.__lst.append(Spacer(0, 30))
						
						textb="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str("").strip())
						col1=Paragraph(textb,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col6=Paragraph(text,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col7=Paragraph(text,stylesheet["BodyText"])
						
						t = Table([[col1,col1,col1,col1,col1,col6,col7,col1]], colWidths=[108,125,15,77,85,140,143,152])
						t.setStyle(ts)
						self.__lst.append(t)
						
						#-----------------------------------------
						self.__lst.append(Spacer(0, 30))
						
						textb="""<para align=left leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str("").strip())
						col1=Paragraph(textb,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col6=Paragraph(text,stylesheet["BodyText"])
						text="""<para align=right leading=6.08><font size=9 face=Helvetica>%s</font></para>""" % (str(self.RoundOff(00.0)).strip())
						col7=Paragraph(text,stylesheet["BodyText"])
						
						t = Table([[col6,col1,col1,col1,col1,col1,col7,col1]], colWidths=[120,113,15,77,85,140,143,152])
						t.setStyle(ts)
						self.__lst.append(t)
						
						self.__lst.append(FrameBreak)

        def CreateReport(self):
                self.__lst = []
                self.CreateContent()

if __name__=='__main__':
        PrintCustomer()