#!/usr/bin/python
# -*- coding: latin-1 -*-

import operator, string
from reportlab.platypus import *
from reportlab.lib.styles import PropertySet, getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.platypus.paragraph import Paragraph
from reportlab.platypus.flowables import PageBreak, Spacer
from reportlab.lib.units import inch
from reportlab.lib.colors import red, black, navy, white, gray
from reportlab.lib.pagesizes import letter, portrait,landscape, A4,legal
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

select = """ select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `atc`, `tin_rdo` from company where id = 1 """
cursor.execute(select)
com = cursor.fetchall()
	
class PrintCustomer:
	def __init__(self):
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=.5*inch, rightMargin=.5*inch,
                                                bottomMargin=1*inch, topMargin=.8*inch,pagesize = portrait(legal)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def myFirstPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('bir.jpg', 30,50, width=560,height=930) 
		
		canvas.setFont('Helvetica-Bold',12)
		canvas.drawString(1* inch, 11.1 * inch, str(com[0][0]))
		
		num = str(com[0][5])
		canvas.setFont('Helvetica',12)
		canvas.drawString(6.98* inch, 11.1 * inch, num[0:1])
		canvas.drawString(7.13* inch, 11.1 * inch, num[1:2])
		canvas.drawString(7.3* inch, 11.1 * inch, num[2:3])
		canvas.drawString(7.48* inch, 11.1 * inch, num[3:4])
		canvas.drawString(7.65* inch, 11.1 * inch, num[4:5])
		canvas.drawString(7.83* inch, 11.1 * inch, num[5:6])
		canvas.drawString(8* inch, 11.1 * inch, num[6:7])
		
		canvas.setFont('Helvetica',11)
		canvas.drawString(1* inch, 10.68 * inch, str(com[0][1]))
		
		postcd = str(com[0][3])
		
		canvas.setFont('Helvetica',11)
		canvas.drawString(7.33* inch, 10.68 * inch, postcd[0:1])
		canvas.drawString(7.54* inch, 10.68 * inch, postcd[1:2])
		canvas.drawString(7.75* inch, 10.68 * inch, postcd[2:3])
		canvas.drawString(7.96* inch, 10.68 * inch, postcd[3:4])
		canvas.drawString(7.33* inch, 10.28 * inch, str(com[0][6]))
		canvas.drawString(6.94* inch, 11.98 * inch, "X")
		canvas.drawString(3.7* inch, 11.98 * inch, "X")
		
		tin = str(com[0][2])
		canvas.setFont('Helvetica-Bold',11)
		canvas.drawString(.92* inch, 11.52 * inch, str(tin[0:1]))
		canvas.drawString(1.12* inch, 11.52 * inch, str(tin[1:2]))
		canvas.drawString(1.29* inch, 11.52 * inch, str(tin[2:3]))
		canvas.drawString(1.62 * inch, 11.52 * inch, str(tin[4:5]))
		canvas.drawString(1.83* inch, 11.52 * inch, str(tin[5:6]))
		canvas.drawString(2* inch, 11.52 * inch, str(tin[6:7]))
		canvas.drawString(2.39* inch, 11.52 * inch, str(tin[8:9]))
		canvas.drawString(2.6* inch, 11.52 * inch, str(tin[9:10]))
		canvas.drawString(2.76* inch, 11.52 * inch, str(tin[10:11]))
		canvas.drawString(3.08* inch, 11.52 * inch, "0")
		canvas.drawString(3.26* inch, 11.52 * inch, "0")
		canvas.drawString(3.45* inch, 11.52 * inch, "0")
		
		rdo = str(com[0][7])
		
		canvas.drawString(4.74* inch, 11.52 * inch, str(rdo[0:1]))
		canvas.drawString(4.9* inch, 11.52 * inch, str(rdo[1:2]))
		canvas.drawString(5.1* inch, 11.52 * inch, str(rdo[2:3]))
		
		canvas.setFont('Times-Roman',9)
		canvas.drawString(7* inch, 0.5 * inch, "Page %d" % doc.page)
				
		canvas.setFont('Times-Roman',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()
			
	def myLaterPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('bir.jpg', 30,50, width=560,height=930) 
		
		canvas.setFont('Times-Roman',9)
		canvas.drawString(7* inch, 0.5 * inch, "Page %d" % doc.page)
				
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

	def CreateContent(self):
		
		
		###########
		
		self.__lst.append(FrameBreak)

        def CreateReport(self):
                self.__lst = []
                self.CreateContent()

if __name__=='__main__':
        PrintCustomer()