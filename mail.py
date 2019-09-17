#~ import time, os, sys
#~ import MySQLdb
import smtplib


#~ def conz():
        #~ mycon = MySQLdb.connect (host = '192.168.1.251', user = 'hris', passwd = 'cradle', db = 'payroll')
        #~ return mycon.cursor()

a = ["ena.belmonte@healthway.com.ph",
"jennifer.lazada@healthway.com.ph",
"jay-jay.tolentino@healthway.com.ph",
"judith.navallo@healthway.com.ph",
"marilyn.serapio@healthway.com.ph",
"bernadette.capili@healthway.com.ph",
"marines.adelan@healthway.com.ph",
"bernadett.ayala@healthway.com.ph",
"avelino.bilan@healthway.com.ph",
"jade.espiritu@healthway.com.ph",
"joan.galicia@healthway.com.ph",
"mybhel.sarmiento@healthway.com.ph",
"norie.nunez@healthway.com.ph",
"michael.cambel@healthway.com.ph",
"rowena.santiago@healthway.com.ph",
"janice.jago@healthway.com.ph",
"abel.delarosa@healthway.com.ph",
"rhea.rodriguez@healthway.com.ph",
"leilani.victoria@healthway.com.ph",
"henry.medina@healthway.com.ph",
"ranel.salamanes@healthway.com.ph",
"ricky.valmores@healthway.com.ph",
"elaine.reyes@healthway.com.ph",
"jun.castillo@healthway.com.ph",
"joy.pasamonte@healthway.com.ph",
"arlon.pilea@healthway.com.ph",
"roda.gatpandan@healthway.com.ph",
"salve.escalante@healthway.com.ph",
"monico.espenida@healthway.com.ph",
"homer.fernandez@healthway.com.ph",
"kristoffer.estrellado@healthway.com.ph",
"marjorie.marticio@healthway.com.ph",
"maricel.montes@healthway.com.ph",
"christopher.bautista@healthway.com.ph",
"melody.legaspi@healthway.com.ph",
"vanessa.pacete@healthway.com.ph",
"jesus.faustino@healthway.com.ph",
"patricio.gatchalian@healthway.com.ph",
"joaquin.burla@healthway.com.ph",
"jerome.aguinoas@healthway.com.ph",
"pam.delossantos@healthway.com.ph",
"rhichille.colibao@healthway.com.ph",
"edmar.gatdula@healthway.com.ph",
"levie.bendicio@healthway.com.ph",
"mariel.estipona@healthway.com.ph",
"babylon.laurel@healthway.com.ph",
"nelly.cano@healthway.com.ph",
"marie.dino@healthway.com.ph",
"elinor.siasoco@healthway.com.ph",
"diana.mangiduyos@healthway.com.ph",
"aileen.retuerto@healthway.com.ph",
"davelyn.laude@healthway.com.ph",
"clarisse.quino@healthway.com.ph",
"jessa.rolle@healthway.com.ph",
"ruby.raval@healthway.com.ph",
"glenn.coronia@healthway.com.ph",
"dante.ricafrente@healthway.com.ph",
"dewynn.rivera@healthway.com.ph",
"joan.alvar@healthway.com.ph",
"vivian.insigne@healthway.com.ph",
"catherine.marron@healthway.com.ph",
"rolando.lanang@healthway.com.ph",
"levie.villar@healthway.com.ph",
"raymund.yaranon@healthway.com.ph",
"reina.formeloza@healthway.com.ph",
"jonalyn.laum@healthway.com.ph",
"romalyn.vino@healthway.com.ph",
"narissa.blas@healthway.com.ph",
"aster.cruz@healthway.com.ph",
"ronn.abary@healthway.com.ph",
"aira.amil@healthway.com.ph",
"marc.grana@healthway.com.ph",
"rony.bondoc@healthway.com.ph",
"rowena.cabillar@healthway.com.ph",
"lea.cuera@healthway.com.ph",
"emilyn.licerio@healthway.com.ph",
"arlene.ytac@healthway.com.ph",
"allan.cabanilla@healthway.com.ph",
"divine.vasquez@healthway.com.ph",
"efren.valdez@healthway.com.ph",
"marjorie.ilustre@healthway.com.ph",
"catherine.prudenciado@healthway.com.ph",
"mariket.custodio@healthway.com.ph",
"ernani.rosaroso@healthway.com.ph",
"kennelyn.baquiran@healthway.com.ph",
"victoria.perez@healthway.com.ph",
"ladylyn.candelario@healthway.com.ph",
"joie.gumabao@healthway.com.ph",
"faina.miguel@healthway.com.ph",
"edward.bayta@healthway.com.ph",
"henamie.tsang@healthway.com.ph",
"jenelyn.cardell@healthway.com.ph",
"ginalyn.manalo@healthway.com.ph",
"marichu.salgado@healthway.com.ph",
"recy.encallado@healthway.com.ph",
"jhonreb.pantoja@healthway.com.ph",
"elnor.estellore@healthway.com.ph",
"jayjay.tolentino@healthway.com.ph",
"dianne.reyes@healthway.com.ph",
"russel.romero@healthway.com.ph",
"maria.cristobal@healthway.com.ph",
"christine.leonor@healthway.com.ph",
"marygold.marquez@healthway.com.ph"]


def mailz(receivers):
	sender = 'epayroll@healthway.com.ph'
	message = """From: From Do Not Reply <epayroll@healthway.com.ph>
	To: To Person <%s>
	MIME-Version: 1.0
	Content-type: text/html
	Subject: Your Payroll Information


	To see your information Please Log on to http://192.168.3.78/hris and Enter you ID No. and Pin No.
	Then goto "My Information".


	Kindly check your tax status and salary if its correct or not. For any question you can ask Arlon Pilea our Payroll Supervisor or Efren Valdez our IT.
		

	Thank you for your Cooperation
		
	Note : Please do not reply this is a system generated email.""" % str(receivers)


        try:
                smtpObj = smtplib.SMTP('localhost')
                smtpObj.sendmail(sender, receivers, message)
                print "Successfully sent email"
        except:
                print "Error: unable to send email"

for x in range(len(a)):
	print str(a[x])
	mailz(str(a[x]))