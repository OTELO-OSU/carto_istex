#!/usr/bin/python
# coding=utf8
#import des différentes dependances python
import sys
import subprocess
import multiprocessing
import time
import json
import os
import pylibmc
import hashlib
import random


def split(arr, size):
     arrs = []
     while len(arr) > size:
         pice = arr[:size]
         arrs.append(pice)
         arr   = arr[size:]
     arrs.append(arr)
     return arrs




def processing(liste,send_end):
    array=[] #Definition tableau vide
    country=None
    mc = pylibmc.Client(["127.0.0.1"]) #Connexion à memcached
    for item in liste:
        if item["country"] is None:
            country="NULL"
        else:
            country = item["country"]
        if len(country)< 30:
            country=country.replace(" ", "", 1)
            Id = item["id"]
            hashed=hashlib.md5()
            hashed.update(country)
            hashed=hashed.hexdigest()
            
            countrycached = mc.get(hashed)
            if countrycached == None:
                script_response = subprocess.Popen(["php","istex/backend/controller/Sender_Nominatim.php",country],stdout=subprocess.PIPE, stderr=subprocess.PIPE)
                (stdout, stderr) = script_response.communicate()
                script_response.wait()
                script_response =stdout
                if "ERREUR" in script_response:
                    script_response='{"country":"NULL","country_code":"NULL","lat":"NULL","lon":"NULL"}'
                if script_response is None:
                    script_response='{"country":"NULL","country_code":"NULL","lat":"NULL","lon":"NULL"}'
                
                mc.set(hashed, script_response,time=864000)
                addid=json.loads(script_response)
                addid['id']=Id
                addid =json.dumps(addid)
                array.append(addid)
            else:
                addid=json.loads(countrycached)
                addid['id']=Id
                addid =json.dumps(addid)
                array.append(addid)
    send_end.send(array)

        

result=[]
pipe_list=[]

def main():
    mc = pylibmc.Client(["127.0.0.1"])
    jsondata = mc.get(sys.argv[1])
    listes= json.loads(jsondata)
    listes=split(listes[1],500)


    for liste in listes:
        recv_end, send_end = multiprocessing.Pipe(False)
        p = multiprocessing.Process(target=processing, args=(liste,send_end))
        pipe_list.append(recv_end)
        p.start()

    for liste in listes:
        p.join(0.1)

    result_list = [x.recv() for x in pipe_list]
    for liste in result_list:
        for item in liste:
            result.append(item)




if __name__ == '__main__':
    main()

print json.dumps(result)






