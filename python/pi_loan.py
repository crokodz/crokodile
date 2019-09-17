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
	
class PrintCustomer:
	def __init__(self):
		self.CreateReport()

                SimpleDocTemplate(file, showBoundary=0, leftMargin=.5*inch, rightMargin=.5*inch,
                                                bottomMargin=1*inch, topMargin=.8*inch,pagesize = portrait(A4)).build(self.__lst,onFirstPage=self.myFirstPage,
                                                onLaterPages=self.myLaterPage)

	def myFirstPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('pi_loan.jpg', 30,660, width=550,height=152) 
		
		#~ canvas.setFont('Times-Roman',9)
		#~ canvas.drawString(7* inch, 0.5 * inch, "Page %d" % doc.page)
				
		canvas.setFont('Times-Roman',7)
		canvas.drawString(.79 * inch, 0.5 * inch, "Run Date %s" % str(datetime.datetime.now()))
		canvas.restoreState()
			
	def myLaterPage(self, canvas, doc):
		canvas.saveState()
		canvas.drawInlineImage('pi_loan.jpg', 30,660, width=550,height=158) 
		
		#~ canvas.setFont('Times-Roman',9)
		#~ canvas.drawString(7* inch, 0.5 * inch, "Page %d" % doc.page)
				
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