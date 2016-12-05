import sys
import subprocess
from threading import Thread
import time
import pprint
import json
import os

def split(arr, size):
     arrs = []
     while len(arr) > size:
         pice = arr[:size]
         arrs.append(pice)
         arr   = arr[size:]
     arrs.append(arr)
     return arrs

result=[]

class Afficheur(Thread):


	def __init__(self, country,Id):
		Thread.__init__(self)
		self.country = country
		self.Id = Id

	def run(self):
		script_response = subprocess.check_output(["php","istex/backend/controller/Sender_Nominatim.php",self.country,self.Id])
		result.append(script_response)



with open('data/'+sys.argv[1]+'.txt') as json_data:
	listes = json.load(json_data)

listes=split(listes,20)
os.remove('data/'+sys.argv[1]+'.txt')

for liste in listes:
	for item in liste[1]:
		thread = Afficheur(item["country"],item["id"])
		thread.start()


for liste in listes:
	for item in liste[1]:
		thread.join()

time.sleep(0.2)
result= json.dumps(result)
result= json.loads(result)
result= json.dumps(result)



print result









