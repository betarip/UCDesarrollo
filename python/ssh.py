import subprocess
import sys
import os
from subprocess import call

def selectAcceso(numero):
    fo = open("accesos.txt", "rw+")
    line = fo.readlines()
    try:
        linea = line[numero]
    except IndexError:
        linea = 'null'
    return linea
    # Close opend file
    fo.close()

def execSSH(HOST, COMMAND):
    ssh = subprocess.Popen(["ssh", "%s" % HOST, COMMAND],
                       shell=False,
                       stdout=subprocess.PIPE,
                       stderr=subprocess.PIPE)
    result = ssh.stdout.readlines()
    if result == []:
        error = ssh.stderr.readlines()
        print >>sys.stderr, "ERROR: %s" % error
        return "Error"
    else:
        return result

def execSCP(HOST, ARCHIVO):
    #Path del archivo
    PATH_SERVER="/home/pi/pruebas/"
    N_ARCHIVO="driver.ino"
    comando = "scp "+ARCHIVO+" pi@192.168.0.106:"+PATH_SERVER+N_ARCHIVO
    os.system(comando)


def stripN(respuestas):
    if type(respuestas) is list:
        res=[]
        for respuesta in respuestas:
            res.append(respuesta.strip('/\n'))
    if type(respuestas) is dict:
        res = "dict"
    if type(respuestas) is str:
        res = "str"
        res = respuestas.strip('\n')
    if type(respuestas) is int:
        res = "str"
    return res

##se da por hecho que la conexion con la raspberry ya se tiene configurada
##con las llaves publicas y privadas

#Seleccionar la puerta
HOST = selectAcceso(0)
HOST = stripN(HOST)
if HOST == 'null':
    print 'error'


##Carga de INO a la raspberry
##definiciones
PATHCOM = '/home/pi/ArduinoDrivers/'




##Subir archivo a la raspberry
PATHARCHIVO="/home/ivan/Documentos/Drivers_Unican/Drivers_Unican.ino"
con = execSCP(HOST,PATHARCHIVO)
##ejecutar el bash en el arduino que mueve el archivo cargado a una carpeta para
##su instalacion
COMMAND=PATHCOM+"instalarDriverPython.sh instalacion"
##carga de archivo a la arduino
con = execSSH(HOST,COMMAND)
print con
if con[0]=='0':
    print "Se instalo correctamente"
else:
    print "Hubo error"





