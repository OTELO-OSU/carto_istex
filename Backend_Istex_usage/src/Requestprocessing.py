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
    tableau_reference_laboratory=["DEPARTMENT","DEPARTAMENTO", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","BRGM","GROUPE DE RECHERCHE","GROUP","GROUPE","BATIMENT","GDR","BUREAU","LABORATORIUM","OFFICE","TEAM","EQUIPE","LPCML","DEVELOPMENT","DEVELOPPEMENT","SERVICE"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
            if len(array)!=2:
                if type(value) is unicode:
                    regex = r"[\[{\(].*[\]}\)]|[[0-9÷\-_@~;:.?+()*-]"
                    laboratory=unidecode.unidecode(value)
                    laboratory=re.sub(regex, " ", laboratory)
                if re.search(r""+reference+"",laboratory.upper()):
                    array.append(laboratory.upper().lstrip())
    return list(set(array))
            

def Match_result_for_university(received_array):
    array=[]
    tableau_reference_university=[" IPGP ","IPG PARIS","CEA","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES","CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","COMMISSARIAT A L'ENERGIE ATOMIQUE","MAX-PLANCK", "MAX PLANCK","IFREMER","UNIVERSITE","ECOLE","UNIVERSITIES","UNIVERSITES","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE"," UNIV ", " INST ", "UNIVERSITY","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD" ,"INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO", "CENTER","CENTRO", "HOSPITAL","HOPITAL", "COLLEGE", "FACULTY","FACULTAD", "COUNCIL", "OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","BRGM","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV","ZENTRUM","CORPORATION","CORP","MINISTRY","MINISTERE","COMPANY","MUSEO","MUSEUM","SURVEY","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM"]
    for reference in tableau_reference_university:
        for value in received_array:
            if len(array)!=2:
                if type(value) is unicode:
                    regex = r"[\[{\(].*[\]}\)]|[[0-9÷\-_@~;:.?+()*-]"
                    university=unidecode.unidecode(value)
                    university=re.sub(regex, " ", university)
                if re.search(r""+reference+"",university.upper()):
                    array.append(university.upper().lstrip())
                

    return list(set(array))

            




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
                    noaffiliation["noaff"]+=1
                    break;
                else:
                    author=value2['name']
                    affiliations=value2['affiliations']
                    if not affiliations is None:
                        if len(affiliations)>=2:
                            if not affiliations[0] is None:
                                parser=affiliations[0].split(',')
                                if re.search(r"(@)", affiliations[0]) or len(parser)==1:
                                    affiliations=affiliations[1].replace("-", ",", 1)
                                    parse = affiliations.split(',')
                                    country = parse[len(parse)-1]
                                else:
                                    affiliations=affiliations[0].replace("-", ",", 1)
                                    parse = affiliations.split(',')
                                    country = parse[len(parse)-1]
                        else:
                            if not affiliations[0] is None: 
                                affiliations=affiliations[0].replace("-", ",", 1)
                                parse = affiliations.split(',')
                                country = parse[len(parse)-1]
                        Id=value["id"]   
                        if parse is not None:   
                            setlaboratory=Match_result_for_laboratory(parse)
                            setuniversity=Match_result_for_university(parse)




                            if len(setlaboratory)!=0:
                                if len(setuniversity)==0:
                                        response_compare=["SCHOOL","ACADEMY","CRPG","LIEC","LSE","GEORESSOURCES"]
                                        if len(setlaboratory)>=2:
                                            for reponse in response_compare:
                                                    if re.search(r""+reponse+"",setlaboratory[0].upper()):
                                                        laboratory=setlaboratory[0]
                                                        university=setlaboratory[1]
                                                    elif re.search(r""+reponse+"",setlaboratory[1].upper()):
                                                        laboratory=setlaboratory[0]
                                                        university=setlaboratory[1]
                                                    
                                        else:
                                            for reponse in response_compare:
                                                if re.search(r""+reponse+"",setlaboratory[0].upper()):
                                                    laboratory=setlaboratory[0]
                                                    university=setlaboratory[0]
                                                     
                                else:
                                    laboratory=setlaboratory[0]          
                                                 
                            else:
                                laboratory=None
                                
                                

                            if len(setuniversity)!=0:
                                if len(setlaboratory)==0:
                                    response_compare=[" CEA ","COMMISSARIAT A L'ENERGIE ATOMIQUE","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES","CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","MAX-PLANCK", "MAX PLANCK","IFREMER","BRGM","CRPG"," UNIV ","UNIVERSITY","UNIVERSITE","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO"," IPGP ","IPG PARIS","CRPG","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM"]
                                    if len(setuniversity)>=2:
                                        for reponse in response_compare:
                                                if re.search(r""+reponse+"",setuniversity[0]):
                                                    laboratory=setuniversity[0]
                                                    university=setuniversity[1]
                                                elif re.search(r""+reponse+"",setuniversity[1]):
                                                    laboratory=setuniversity[0]
                                                    university=setuniversity[1]
                                                   
                                                

                                    else:
                                        for reponse in response_compare:
                                            if re.search(r""+reponse+"",setuniversity[0]):
                                                laboratory=setuniversity[0]
                                                university=setuniversity[0]

                                else:
                                    university=setuniversity[0]

                                
                            else:
                                university=None


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
