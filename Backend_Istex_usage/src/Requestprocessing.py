#!/usr/bin/python
# coding=utf8
import sys
import subprocess
from threading import Thread
import time
import json
import os
import hashlib
import pylibmc
import re
import json
from pprint import pprint
import urllib
import string
import multiprocessing 
import unidecode
from fuzzywuzzy import fuzz
from fuzzywuzzy import process


  



def split(arr, size):
     arrs = []
     while len(arr) > size:
         pice = arr[:size]
         arrs.append(pice)
         arr   = arr[size:]
     arrs.append(arr)
     return arrs



def Match_result_for_laboratory(received_array):
    
    array=[]
    tableau_reference_laboratory=["DEPARTMENT", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE","BRGM"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
            if type(value) is unicode:
                regex = r"[\[{\(].*[\]}\)]|[[0-9÷\-_@~;:.?+)*-]"
                laboratory=unidecode.unidecode(value)
                laboratory=re.sub(regex, " ", laboratory)
            if re.search(r""+reference+"",laboratory.upper()):
                array=laboratory.upper().lstrip()
                return array
            

def Match_result_for_university(received_array):
    array=[]
    tableau_reference_university=[" UNIV ", " INST ", "UNIVERSITY", "INSTITUTE", "INSTITUTION", "CENTER", "HOSPITAL", "COLLEGE", "FACULTY", "COUNCIL", "CEA", "MAX PLANK","IFREMER","UNIVERSITE","ECOLE","UNIVERSITIES","UNIVERSITES","OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","BRGM","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV"]
    for reference in tableau_reference_university:
        for value in received_array:
            if type(value) is unicode:
                regex = r"[\[{\(].*[\]}\)]|[[0-9÷\-_@~;:.?+)*-]"
                university=unidecode.unidecode(value)
                university=re.sub(regex, " ", university)
            if re.search(r""+reference+"",university.upper()):
                array=university.upper().lstrip()
                return array
            




def processing(liste,send_end):
    noaffiliation={"noaff":0}
    data=liste
    country=None
    parse=None
    global laboratory 
    laboratory=None
    global university
    university=None
    for value in data:
        if not 'author' in value:
            noaffiliation["noaff"]+=1

        else:
            for value2 in value["author"]:
                if not 'affiliations' in value2:
                    noaf=None
                else:
                    author=value2['name']
                    affiliations=value2['affiliations']
                    if not affiliations is None:
                        if len(affiliations)==2:
                            if not affiliations[0] is None:
                                if re.search(r"(^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$)", affiliations[0]):
                                    parse = affiliations[1].split(',')
                                    country = parse[len(parse)-1]
                                else:
                                    parse = affiliations[0].split(',')
                                    country = parse[len(parse)-1]
                        else:
                            if not affiliations[0] is None: 
                                parse = affiliations[0].split(',')
                                country = parse[len(parse)-1]
                        Id=value["id"]   
                        if parse is not None:   
                            laboratory=Match_result_for_laboratory(parse)
                            university=Match_result_for_university(parse)
                           

                        if country is not None:
                            country.replace(".", "", 1)
                            regex = r"[\[{\(].*[\]}\)]|[0-9÷\-_@~;:.?'+*-]"
                            if type(country) is unicode:
                                
                                country=country.upper()
                                country=country.replace(" ", "", 1)
                                country=re.sub(regex, "", country)
                                country=urllib.quote(country.encode('utf-8'))

                        array={}
                        array["id"]=Id
                        array["country"]=country
                        
                        array["laboratory"]=laboratory
                           
                        
                        array["university"]=university
                        array["author"]=author

                        response_array.append(array)
    arrayaff=[]
    arrayaff.append(noaffiliation)
    array=[]
    array.append(arrayaff)
    array.append(response_array)
    send_end.send( array)


response_array=[]
result=[]

pipe_list = []
arraytmp={}

def main():
    mc = pylibmc.Client(["127.0.0.1"])
    jsondata = mc.get(sys.argv[1])
    listes_re= json.loads(jsondata)
    listes=split(listes_re,200)

    for liste in listes:
        recv_end, send_end = multiprocessing.Pipe(False)
        p = multiprocessing.Process(target=processing, args=(liste,send_end))
        pipe_list.append(recv_end)
        p.start()
    for liste in listes:
        p.join()

    result_list = [x.recv() for x in pipe_list]
    noaff=0
    array=[]
    for liste in result_list:
        noaff+=liste[0][0]["noaff"]
        result_liste=liste[1]
        for i in range(0,len(result_liste)):
            for n in range(1,len(result_liste)):
                comparelabo=result_liste[i]["laboratory"]
                compareuniv=result_liste[i]["university"]
                comparelabo2=result_liste[n]["laboratory"]
                compareuniv2=result_liste[n]["university"]

                if (result_liste[i]["laboratory"]!=result_liste[n]["laboratory"]) or (result_liste[i]["university"]!=result_liste[n]["university"]) :
                    
                    

                    labopercent=fuzz.partial_ratio(comparelabo,comparelabo2)
                    univlabo= fuzz.partial_ratio(compareuniv,compareuniv2)
                    if labopercent>=90 and univlabo>=90:
                        result_liste[i]["university"]=compareuniv
                        result_liste[n]["university"]=compareuniv
                        result_liste[i]["laboratory"]=comparelabo
                        result_liste[n]["laboratory"]=comparelabo
    
    for liste in result_list:
        for item in liste[1]:
            result.append(item) 


    arraytmp["noaff"]=noaff
    arraytmp["total"]=len(listes_re)

if __name__ == '__main__':
    main()



response_array=[]
response_array.append(arraytmp)
response_array.append(result)



print json.dumps(response_array)
