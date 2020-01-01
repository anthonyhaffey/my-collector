import eel
import os
import json
import tkinter as tk

from tkinter import filedialog
from tkinter import *
from tkinter.filedialog import askopenfile
from tkinter.messagebox import showerror

import base64
import pandas as pd
import numbers
import numpy as np

@eel.expose
def create_space(repository_name,
                 github_organisation,
                 github_username,
                 github_password):
    print(repository_name)
    print(github_username)
    if github_organisation != "":
        repository_dir = github_organisation + "/" + repository_name
    else:
        repository_dir = repository_name

    orig_dir = os.getcwd();
    #os.mkdir("web/" + repository_name)
    os.chdir(orig_dir + "/web")

    #root = tk.Tk()
    #root.withdraw()
    #file_path = filedialog.askdirectory(title="Where shall we install Collector?")
    #print(file_path);
    #os.chdir(file_path)
    os.system("git clone https://github.com/open-collector/open-collector " + repository_name)


    print("You now have a working version of Collector!")
    print("repository_name")
    print(repository_name)
    os.chdir(repository_name)
    os.system("hub create " + repository_dir)
    print("git push https://" + github_username + ":" + github_password + "@github.com/" + repository_dir + ".git")
    os.system("git push https://" + github_username + ":" + github_password + "@github.com/" + repository_dir + ".git")
    eel.python_message("Succesfully updated online version of collector!")

    os.chdir(orig_dir)
    settings = open("settings.json", "r+")
    read_settings = json.loads(settings.read())
    read_settings[repository_name] = {
        "online": "true",
        "organisation": github_organisation,
        "repository": repository_name,
        "username": github_username
    }
    settings.close()

    settings = open("settings.json", "r+")
    settings.write(json.dumps(read_settings))
    settings.close()

files_folder = os.listdir()
@eel.expose
def startup():
    if "settings.json" in files_folder:
        settings = open("settings.json","r")
        eel.load_settings(settings.read())

@eel.expose
def update_collector(location,
                     this_rep_info,
                     password):
    this_rep_info = json.loads(this_rep_info)
    organisation  = this_rep_info["organisation"]
    repository    = this_rep_info["repository"]
    username      = this_rep_info["username"]

    if organisation == "":
        organisation = username

    #repository = split the location to get the repository name
    os.chdir(location)
    os.system("git pull https://github.com/open-collector/open-collector")

    eel.python_message("Succesfully updated on local machine!")

    if this_rep_info["online"]:
        os.system("git add .")
        os.system("git commit -m 'update from master'")
        os.system("git push https://" + username + ":" + password + "@github.com/" + organisation + "/" + repository)
        eel.python_message("Succesfully updated <b>" + organisation + "/" + repository)
        #git push https://username:password@myrepository.biz/file.git --all




@eel.expose
def load_master_json():
    #check if the uber mega file exists yet
    try:
        master_json = open("kitten/Local/master.json", "r")
        master_json = master_json.read();
    except:
        master_json = open("web/kitten/Default/master.json", "r")
        master_json = master_json.read()
        master_json = json.loads(master_json)
        #add default experiments - not yet present

        #add default surveys
        #default_surveys = os.listdir("web/" + user_repository + "/kitten/Default/DefaultSurveys")

        #for default_survey in default_surveys:
        #    this_survey = open("web/" + user_repository + "/kitten/Default/DefaultSurveys/" + default_survey)
        #    uber_mega_json[]

        #add default trialtypes


    finally:
        # if not, then proceed with the default uberMega.json
        eel.load_master_json(master_json)

@eel.expose
def save_master_json(master_json):
    master_file = open("Local/master.json", "w")
    master_file .write(master_json)

eel.init('web') #allowed_extensions=[".js",".html"]
eel.start('kitten/index.html')
