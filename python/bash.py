import subprocess
import sys
import os
# ...

result=subprocess.call("./prueba.sh")
if result == []:
    error = bash.stderr.readlines()
    print >>sys.stderr, "ERROR: %s" % error
    print "Error"
else:
    print "Hla"

    #COMMAND=PATHCOM+"listaArchivos.sh"
#con = execSSH(HOST,COMMAND)
#con = stripN(con)
#print con
#n = input("Carpeta: ")
#COMMAND=PATHCOM+"selectFile.sh "+str(n)
#con = execSSH(HOST,COMMAND)
#con = stripN(con)
#print con


#ejecucion de los comandos de instalacion
##PRUEBAS SIN CONFIGURACION
#PATHCOM = '/home/ivan/bash/'
#HOST="ivan@192.168.124.192"
#COMMAND=PATHCOM+"instalarDriverPython.sh BlinkPrueba"


#con = execSSH(HOST,COMMAND)
#print con
#if con[0]=='0':
    #print "Se instalo correctamente"
#else:
    #print "Hubo error"
