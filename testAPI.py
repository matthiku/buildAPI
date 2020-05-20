import requests, datetime, signal, sys, json
import random
import webbrowser, copy
from collections import OrderedDict


# url list
lclWUrl = "http://localhost/dev/buildingAPI/public/"                     # test
lclUrl = "http://buildapi.me/api/"                     # test
rmtUrl = "http://buildingapi.eec.ie/"     # prod

# output file for extensive (non-JSON) html replies
tmpfile = '/laragon/www/pyout.html'

# random secret in the API db
lcl_client_secret = "SULrmfBV"
lcl_client_secret = "WQN1VgH5Oy2VTjCNwH3HCTQpNc3wqJtPqMKSmFas"
rmt_client_secret = "RYGnyjKPs"
client_secret = lcl_client_secret

headers = {'Accept' : ''}
headers['Accept'] = 'application/json'

# FUNCTIONS
                        
def signal_handler(signal, frame):
    ''' handle keyboard interrupts '''
    print('Program gracefully ended.')
    sys.exit(0)
# configure the above function as the signal handler
signal.signal(signal.SIGINT, signal_handler)
signal.signal(signal.SIGTERM, signal_handler)

'''  print JSON formatted data '''
def jsonPrint(item, indent, level):
    
    if type(item) is dict:
        level+=1
        print(' ')
        maxw = 1
        for w in item:
            maxw=max(len(w),maxw)
        for w in item:
            if indent==0: indent = len(w)+2
            print(' '*indent*level+w.ljust(maxw)+': ',end='')
            jsonPrint(item[w], indent, level)
            
    elif type(item) is list:
        level+=1
        print(' ')
        for w in item:
            jsonPrint(w, indent, level)
                
    else:
        print(item)

''' write html result into HTML file '''
def nonJson(text):
    if len(text)>500:
        htmldoc = open(tmpfile, 'w')
        htmldoc.write(r.text)
        htmldoc.close()
        print("Result written into temp file", tmpfile)
        # launch this file in your browser
        url = 'file://'+tmpfile
        webbrowser.open_new_tab( url+'pyout.html' )
    else:
        print(text)


''' get user to select environment '''
def getEnviron():
    print('-'*50)
    print("L:",lclUrl)
    print("W:",lclWUrl)
    print("R:",rmtUrl)
    while True:
        where = input("Local or remote? (L/r) ").upper()
        if where in ("W", "L","R", ""): break
    url = lclUrl
    if where == "R": 
        url = rmtUrl
        tokenRequest['client_secret'] = rmt_client_secret
    if where == "W": 
        url = lclWUrl
        tokenRequest['client_secret'] = rmt_client_secret
    print('Using:', url, "\n")
    return url

''' get user to select API command '''
def inputCmd():
    # which API command?
    for i in cmds:
        print(i.rjust(3)+": ", cmds[i][0].ljust(7), cmds[i][1])

    while True:
        cmd = input('['+url+']'+" Enter selection: ")
        if cmd=='': sys.exit()
        try: x=len(cmds[cmd])
        except: continue
        break

    # request missing parameter if API command contains a '?'
    if (cmds[cmd][1].find('?')>0):
        print("API command:", cmds[cmd][1])
        parm = input("Enter missing parameter for this command: ")
        cmds[cmd][1] = cmds[cmd][1].replace('?',parm)
    print('-'*60)
    return cmd

    
''' request a new access token '''
def getToken():
    r = requests.post(url+'login', data={'email':'matthiku@yahoo.com', 'password':'test0123'}, headers=headers)
    rc = r.status_code
    if rc == 200:
        return r.json()['access_token'], r.json()['expires_in']
    if rc == 401:
        jsonPrint( r.json(), 0, 0 )
        print('\n'+'-'*50)
        return '', 0
    print('Status code:', r.status_code)    
    print(r.text)
    
''' create data payload as dict and include the access token management '''
def getPayload(expire, accToken):
    now = datetime.datetime.now().timestamp()
    print('(getPayLoad) Expires: ' + str(expire) + ' now: ' + str(now))
    # check if token has expired
    if expire - now < 1:
        # get access token first
        accToken, expire = getToken()
        if accToken == '': return 0,0,0
        # add access token to the header
        headers['Authorization'] = 'Bearer ' + accToken
        # expire = now + expires_in
        print("Token expires at ", datetime.datetime.fromtimestamp(expire).isoformat())
    print( "(getPayLoad) requesting", action+":", url + cmds[cmd][1] )            
    # aquire the payload and append the access_token
    payload = cmds[cmd][2].copy()
    payload['access_token'] = accToken
    # print("Payload is:", payload)p1
    return payload, expire, accToken

now = datetime.datetime.now()

# form data
newEvent = {
    'seed'      : random.randint(10000,99999),
    'title'     : 'This is the NEW title',
    'rooms'     : random.randint(1,2),
    'status'    : 'OK',
    'start'     : '12:34',
    'end'       : '13:24',
    'nextdate'  : ( now + datetime.timedelta( days=random.randint(1,31)) ).strftime("%Y-%m-%d"),
    'repeats'   : 'weekly',
    'weekday'   : 'Monday',
    'targetTemp': random.randint(17,23),
}
tokenRequest = {
    'grant_type'    : 'client_credentials',
    'client_id'     : 'collector',
    'client_secret' : client_secret,
}
newPowerLog = {
    'power'       : random.randint(300,399),
    'boiler_on'   : 0,
    'heating_on'  : 0,
    'updated_at'  : datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
}
newTempLog = {
    'mainroom'   : random.randint(17,23),
    'auxtemp'    : random.randint(17,23),
    'frontroom'  : random.randint(17,23),
    'heating_on' : '0',
    'power'      : random.randint(300,399),
    'outdoor'    : random.randint(17,23),
    'babyroom'   : random.randint(17,23),
}
newEventLog = {
    'event_id'   : 3,
    'eventStart' : '10:00',
    'estimateOn' : '11:11',
    'actualOn'   : '12:22',
    'actualOff'  : '13:33',  # optional...
}
newBuildingLog = {
    'what'  : 'ruelps',
    'where' : 'switch',
    'text'  : 'off',
}
    
#-------------------------------------
# create a list of API commands
#-------------------------------------
#
# TODO: replace this with the route.php content....
#
# use ordered dictionary to retain order as given
cmds = OrderedDict()
#
# get all resources or a certain one
cmds['0'] = ['GET', 'settings', {}]
cmds['1'] = ['GET', 'events',   '']
cmds['2'] = ['GET', 'events/?', '']
# get all resources by a specific status
cmds['3'] = ['GET', 'events/status/?', '']

# get latest POWERlog result
cmds['4'] = ['GET', 'powerlog/latest', '']
# get latest TEMPlog result
cmds['5'] = ['GET', 'templog/latest', '']
# get latest EVENTlog result
cmds['6'] = ['GET', 'eventlog/latest', '']
# get latest EVENTlog result
cmds['7'] = ['GET', 'buildinglog/latest', '']

# acquire access token
cmds['t'] = ['POST', 'oauth/access_token', tokenRequest]

# create a new EVENT resource
cmds['p1'] = ['POST', 'events',   newEvent   ]

# create a new POWERlog resource
cmds['p4'] = ['POST', 'powerlog', newPowerLog]
# create a new TEMPlog resource
cmds['p5'] = ['POST', 'templog',  newTempLog ]
# create a new EVENTlog resource
cmds['p6'] = ['POST', 'eventlog', newEventLog]
# create a new BUILDINGlog resource
cmds['p6'] = ['POST', 'buildinglog', newBuildingLog]

# delete a resource
cmds['d1'] = ['DELETE', 'events/?', {}]
# update a resource
cmds['u1'] = ['PATCH',  'events/?', newEvent]

today = datetime.date.today()
diff = datetime.timedelta( days = 7 )
nextEventDate = (today + diff).strftime("%Y-%m-%d")

# update only the nextDate field on an event resource
cmds['u2'] = ['PATCH',  'events/?/nextdate/'+nextEventDate, {}]

origCmds = copy.deepcopy(cmds)

# set token expiration time to now, so that 
# we have to request a new token immediately
expire = datetime.datetime.now().timestamp()
accToken = ''

print('-'*50, "RESTful API Test Machine\n(hit 'Ctrl'+c to stop the program)\n")


# Get user to select REMOTE (production) or LOCAL (development)
url = getEnviron()


#
# main loop
#
while True:
    r = ''              # init the request var
    cmds = copy.deepcopy(origCmds)     # make sure we have a clean sheet...

    # Which API command to test?
    print('-'*50)
    cmd    = inputCmd()
    action = cmds[cmd][0]
    
    try:
        # execute GET command without OAuth
        if action == "GET" and not cmds[cmd][1] == 'settings':
            print("requesting GET:",url+cmds[cmd][1])
            r = requests.get(url + cmds[cmd][1], headers=headers)

        else:
            payload, expire, accToken = getPayload( expire, accToken )
            if payload == 0: 
                print('Error occurred, unable to continue.')
                break

        # for the settings, we always need OAuth
        if cmds[cmd][1] == 'settings':
            r = requests.get( url + cmds[cmd][1] + '?access_token=' + payload['access_token'], headers=headers )

        # execute POST command with data
        if action == "POST":
            r = requests.post( url + cmds[cmd][1], data=payload, headers=headers )

        # execute DELETE command
        if action == "DELETE":
            r = requests.delete( url + cmds[cmd][1], data=payload, headers=headers )
            
        # execute PATCH command
        if action == "PATCH":
            r = requests.patch( url + cmds[cmd][1], data=payload, headers=headers )

    except ConnectionError as  e:
        print('-'*50+" 1\nConnection to", url, "failed. ConnectionError. Try again later:\n", e)
        continue
    except TypeError as  e:
        print('-'*50+" 2\nConnection to", url, "failed. TypeError. Try again later:\n", e)
        continue
    except requests.exceptions.ConnectionError as  e:
        print('-'*50+" 4\nConnection to", url, "failed. OtherError. Try again later:\n", e)
        continue


    #-------------------------------------
    # show the results
    #-------------------------------------
    rc = r.status_code
    print('='*50, "RESULT:\nHTML status code:", rc)
    try:
        jsonPrint( r.json(), 0, -1 )
    except:
        nonJson( r.text )
    print('='*50)
        

print("Good bye!")
