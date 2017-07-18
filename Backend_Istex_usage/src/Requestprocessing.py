#!/usr/bin/python
# coding=utf8
import sys
import subprocess
import json
import os
import hashlib
import pylibmc
import re
import string
import multiprocessing 
import unicodedata



#Fonction split  
#param arr:array to split
#param size: size of each array 
#return: Array of splitted array
def split(arr, size):
     arrs = []
     while len(arr) > size:
         pice = arr[:size]
         arrs.append(pice)
         arr   = arr[size:]
     arrs.append(arr)
     return arrs



#Fonction Match_result_for_laboratory
#param received_array:array(affiliations) 
#return: Array of match
#labo-all
def Match_result_for_laboratory(received_array):
    array=[]
    tableau_reference_laboratory=["DEPARTMENT","DEPARTAMENTO", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","ECOLE","GROUPE DE RECHERCHE","GROUP","GROUPE","BATIMENT","GDR","BUREAU","LABORATORIUM","OFFICE","TEAM","EQUIPE","LPCML","DEVELOPMENT","DEVELOPPEMENT","SERVICE"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
                laboratory=value
                if reference in laboratory:
                    return laboratory.lstrip()
                
#labo-service             
def Search_for_labo(received_array,received_laboratory):
    array=[]
    tableau_reference_laboratory=["SCHOOL", "ACADEMY"]
    for reference in tableau_reference_laboratory:
        for value in received_array:
                    laboratory=value
                    if reference in laboratory:
                        if received_laboratory!=laboratory.lstrip():
                            return laboratory.lstrip()
            
#Institution-all
def Match_result_for_university(received_array):
    array=[]
    tableau_reference_university=[" UNIV ", "UNIVERSITY","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD" , " INST ","INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO","UNIVERSITE","UNIVERSITIES","UNIVERSITES","MAX-PLANCK", "MAX PLANCK","IFREMER","MUSEUM","SURVEY","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM","BRGM","CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","COMMISSARIAT A L'ENERGIE ATOMIQUE","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE", "FACULTY","FACULTAD","FACULTE","CENTER","CENTRO", "HOSPITAL","HOPITAL", "COLLEGE", "COUNCIL", "OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","NATIONAL LABORATORY"," IPGP ","IPG PARIS"," CEA ","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV","ZENTRUM","CORPORATION","CORP","MINISTRY","MINISTERE","COMPANY","MUSEO"]
    for reference in tableau_reference_university:
        for value in received_array:
                university=value
                if reference in university:
                    if "CNRS" in  filter(str.isupper, university): 
                        university="CNRS"
                    return university.lstrip()
                

            
#Institution-Major
def Search_for_university(received_array):
    array=[]
    tableau_reference_university=[ "COMMISSARIAT A L'ENERGIE ATOMIQUE","BRGM"," IPGP ","IPG PARIS"," CEA ","CENTRE NATIONALE POUR LA RECHERCHE SCIENTIFIQUE","COMMISSARIAT A L'ENERGIE ATOMIQUE","UNIVERSITE","UNIVERSITIES","UNIVERSITES","CNRS"," CNRS "," C.N.R.S ","C.N.R.S","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE"," UNIV ", " INST ", "UNIVERSITY","UNIVERSITAT","UNIVERSITA","UNIVERSIDAD" ,"INSTITUTE","INSTITUT", "INSTITUTION","INSTITUTO","BRGM"," IPGP ","IPG PARIS"," CEA ","CENTRE DE RECHERCHES PETROGRAPHIQUES ET GEOCHIMIQUES","UNIVERSITA","MAX-PLANCK", "MAX PLANCK","IFREMER","INRA","IRD","IRSTEA","CEMAGREF","INRIA","INED","IFSTAR","INSERM"]
    for reference in tableau_reference_university:
        for value in received_array:
                university=value
                if reference in university:
                    if "CNRS" in  filter(str.isupper, university): 
                        university="CNRS"
                    return university.lstrip()                      

#Institution-institution
def Search_for_university_labo_and_inst(received_array):
    array=[]
    tableau_reference_university=["COMMISSARIAT A L'ENERGIE ATOMIQUE","BRGM"," IPGP ","IPG PARIS"," CEA "]
    for reference in tableau_reference_university:
        for value in received_array:
                university=value
                if reference in university:
                    return university.lstrip()
                
               
#Institution-Labo
def Search_for_university_labo(received_array,received_university):
    array=[]
    tableau_reference_university=["CENTER","CENTRO", "HOSPITAL","HOPITAL", "COLLEGE", "FACULTY","FACULTAD","FACULTE", "COUNCIL", "OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV","ZENTRUM","CORPORATION","CORP","MINISTRY","MINISTERE","COMPANY","MUSEO"]
    for reference in tableau_reference_university:
        for value in received_array:
                university=value
                if reference in university:
                    if received_university!=university:
                        return university.lstrip()
                
                



#Fonction processing  
#param liste:list of data to parsed
#param send_end: data return to merge with other process
#return: send_end to parent process

def processing(liste,send_end):
    noaffiliation={"noaff":0}
    data=liste
    country=None
    parse=None
    
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
                    if 'title' in value:
                        title=value["title"]
                    else:
                        title="null"
                    author=author.upper()
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
                            university=None
                            laboratory=None  
                            laboratory=Match_result_for_laboratory(parse)
                            if laboratory is None:
                                laboratory=Search_for_university(parse)
                                if laboratory is None:
                                    laboratory=None
                                else:
                                    laboratory=Search_for_university_labo(parse,university)
                                    if laboratory==None:
                                        laboratory=Search_for_university_labo_and_inst(parse)
                                
                            else:
                                university=Match_result_for_university(parse)

                            if university is None:
                                university=Search_for_labo(parse,laboratory)
                            if university is None and laboratory is not None:
                                university=Match_result_for_university(parse)
                        else:
                            laboratory=None
                            university=None

                        if country is not None:
                            country.replace(".", "", 1)
                            regex = r"[\[{\(].*[\]}\)]|[0-9÷\-_@~;:.?'+*-]"
                                
                            country=country.upper()
                            country=country.replace(" ", "", 1)
                            country=re.sub(regex, "", country)



                        array={}
                        array["id"]=Id
                        array["country"]=country
                        array["laboratory"]=laboratory
                        array["university"]=university
                        array["author"]=author
                        array["title"]=title


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


#Fonction main

def main():
    mc = pylibmc.Client(["memcached"])
    jsondata = mc.get(sys.argv[1])
    listes_re= json.loads(jsondata)
    listes=split(listes_re,1000)

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
