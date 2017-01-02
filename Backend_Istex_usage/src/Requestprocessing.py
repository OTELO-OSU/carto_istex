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
import unicodedata



  



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
    tableau_reference_laboratory=["DEPARTMENT","DEPARTAMENTO", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","ECOLE","GROUPE DE RECHERCHE","GROUP","GROUPE","BATIMENT","GDR","BUREAU","LABORATORIUM","OFFICE","TEAM","EQUIPE","LPCML","DEVELOPMENT","DEVELOPPEMENT","SERVICE"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
                
                laboratory=value
                
                if reference in laboratory:
                    return laboratory.lstrip()
                
def Search_for_labo(received_array,received_laboratory):
    array=[]
    tableau_reference_laboratory=["SCHOOL", "ACADEMY"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
                if type(value) is unicode:
                    laboratory=value
                    if reference in laboratory:
                        if received_laboratory!=laboratory.lstrip():
                            return laboratory.lstrip()
            

def Match_result_for_university(received_array):
    array=[]
    tableau_reference_university=["CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","COMMISSARIAT A L'ENERGIE ATOMIQUE","UNIVERSITE","UNIVERSITIES","UNIVERSITES","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE"," UNIV ", "UNIVERSITY","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD" , " INST ","INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO", "CENTER","CENTRO", "HOSPITAL","HOPITAL", "COLLEGE", "FACULTY","FACULTAD", "COUNCIL", "OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","BRGM","NATIONAL LABORATORY"," IPGP ","IPG PARIS"," CEA ","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV","ZENTRUM","CORPORATION","CORP","MINISTRY","MINISTERE","COMPANY","MUSEO","MAX-PLANCK", "MAX PLANCK","IFREMER","MUSEUM","SURVEY","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM"]
    for value in received_array:
        for reference in tableau_reference_university:
                university=value
                if reference in university:
                    if "CNRS" in  filter(str.isupper, university): 
                        university="CNRS"
                    return university.lstrip()
                

            

def Search_for_university(received_array):
    array=[]
    tableau_reference_university=[ "COMMISSARIAT A L'ENERGIE ATOMIQUE","BRGM"," IPGP ","IPG PARIS"," CEA ","CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","COMMISSARIAT A L'ENERGIE ATOMIQUE","UNIVERSITE","UNIVERSITIES","UNIVERSITES","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE"," UNIV ", " INST ", "UNIVERSITY","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD" ,"INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO","BRGM"," IPGP ","IPG PARIS"," CEA ","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES","UNIVERSITA","MAX-PLANCK", "MAX PLANCK","IFREMER","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM"]
    for value in received_array:
        for reference in tableau_reference_university:
                if type(value) is unicode:
                    if reference in university:
                        if "CNRS" in  filter(str.isupper, university): 
                            university="CNRS"
                        return university.lstrip()                      


def Search_for_university_labo_and_inst(received_array):
    array=[]
    tableau_reference_university=["COMMISSARIAT A L'ENERGIE ATOMIQUE","BRGM"," IPGP ","IPG PARIS"," CEA "]
    for value in received_array:
        for reference in tableau_reference_university:
                if type(value) is unicode:
                    university=value
                if reference in university:
                    return university.lstrip()
                
               

def Search_for_university_labo(received_array,received_university):
    array=[]
    tableau_reference_university=["CENTER","CENTRO", "HOSPITAL","HOPITAL", "COLLEGE", "FACULTY","FACULTAD", "COUNCIL", "OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV","ZENTRUM","CORPORATION","CORP","MINISTRY","MINISTERE","COMPANY","MUSEO"]
    for value in received_array:
        for reference in tableau_reference_university:
                if type(value) is unicode:
                    university=value
                if reference in university:
                    if received_university!=university:
                        return university.lstrip()
                
                





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
                    regex = r"[\[{\(].*[\]}\)]|[[0-9÷\-_@~;:.?+()*-]"
                    if not affiliations is None:
                        if len(affiliations)>=2:
                            if not affiliations[0] is None:
                                parser=affiliations[0].split(',')
                                if re.search(r"(@)", affiliations[0]) or len(parser)==1:
                                        if affiliations[1] is not None:
                                            affiliations=affiliations[1].replace("-", ",", 1)
                                            affiliations=affiliations.replace(";", ",", 1)
                                            affiliations=affiliations.upper()
                                            affiliations=re.sub(regex, " ", affiliations)
                                            affiliations=unicodedata.normalize('NFKD', affiliations).encode('ascii','ignore')
                                            parse = affiliations.split(',')
                                            country = parse[len(parse)-1]
                                else:
                                    affiliations=affiliations[0].replace("-", ",", 1)
                                    affiliations=affiliations.replace(";", ",", 1)
                                    affiliations=affiliations.upper()
                                    affiliations=re.sub(regex, " ", affiliations)
                                    affiliations=unicodedata.normalize('NFKD', affiliations).encode('ascii','ignore')
                                    parse = affiliations.split(',')
                                    country = parse[len(parse)-1]
                        else:
                            if not affiliations[0] is None: 
                                affiliations=affiliations[0].replace("-", ",", 1)
                                affiliations=affiliations.replace(";", ",", 1)
                                affiliations=affiliations.upper()
                                affiliations=re.sub(regex, " ", affiliations)
                                affiliations=unicodedata.normalize('NFKD', affiliations).encode('ascii','ignore')
                                parse = affiliations.split(',')
                                country = parse[len(parse)-1]
                        Id=value["id"] 
                        if parse is not None:  
                            laboratory=Match_result_for_laboratory(parse)
                            university=Match_result_for_university(parse)
                            if laboratory is None:
                                laboratory=Search_for_university(parse)
                                if laboratory is None:
                                    laboratory=None
                                else:
                                    laboratory=Search_for_university_labo(parse,university)
                                    if laboratory==None:
                                        laboratory=Search_for_university_labo_and_inst(parse)
                                        
                                
                            if university is None:
                                university=Search_for_labo(parse,laboratory)

                               
                                




                           


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
        p.join(0.1)

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
