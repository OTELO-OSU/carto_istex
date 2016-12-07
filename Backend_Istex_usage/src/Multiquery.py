#!/usr/bin/python
import sys
import subprocess
from threading import Thread
import time
import json
import os
import pylibmc
import hashlib


def split(arr, size):
     arrs = []
     while len(arr) > size:
         pice = arr[:size]
         arrs.append(pice)
         arr   = arr[size:]
     arrs.append(arr)
     return arrs

result=[]

class Send(Thread):


	def __init__(self, country,Id):
		Thread.__init__(self)
		self.country = country
		self.Id = Id

	def run(self):
		mc = pylibmc.Client(["127.0.0.1"])
		hashed=hashlib.md5()
		hashed.update(self.country)
		hashed=hashed.hexdigest()
	
		country = mc.get(hashed)
		if country is None:
			script_response = subprocess.check_output(["php","istex/backend/controller/Sender_Nominatim.php",self.country])
			mc.set(hashed, script_response)
			addid=json.loads(script_response)
			addid['id']=self.Id
			addid =json.dumps(addid)
			result.append(addid)
			pass
		else:
			
			addid=json.loads(country)
			addid['id']=self.Id
			addid =json.dumps(addid)
			result.append(addid)
			pass

		
	
		

mc = pylibmc.Client(["127.0.0.1"])
jsondata = mc.get(sys.argv[1])
listes= json.loads(jsondata)
listes=split(listes,200)


for liste in listes:
	for item in liste[1]:
		thread = Send(item["country"],item["id"])
		thread.start()


for liste in listes:
	for item in liste[1]:
		thread.join()


time.sleep(0.1)
results= json.dumps(result)
results= json.loads(results)
results= json.dumps(results)


print results






