# quick hack:
# grabs data from XML file describing opcodes from http://ref.x86asm.net
# then autocomments the cpux86 emulator code
#
# (super brittle hack)
#

from BeautifulSoup import BeautifulStoneSoup #thank you soup, fuck XML parsers
import json, re

#
# Let me reiterate how much I despise scraping data from XML
#
infile = open("x86reference.xml","r").read()
soup=BeautifulStoneSoup(infile)
onesies=soup.find('one-byte').findAll('pri_opcd')
twosies=soup.find('two-byte').findAll('pri_opcd')

def hexRepOfOp(op):
    i=int(op['value'],16)
    if i < 16:
        return ("0x0"+hex(i)[2:]).lower()
    else:
        return ("0x" +hex(i)[2:]).lower()
def mnem(op):
    res = op.find('mnem')
    if res:
        return res.string
    else:
        return ""
def src(op):
    res = op.find('syntax').find('src')
    if res:
        return res.getText()
    else:
        return ""
def dst(op):
    res = op.find('syntax').find('dst')
    if res:
        return res.getText()
    else:
        return ""
def note(op):
    res = op.find('note').find('brief')
    if res:
        return res.getText()
    else:
        return ""
def opstr(op):
    return mnem(op)+" "+src(op)+" "+dst(op)+" "+note(op)

onedict = {}
for op in onesies:
    onedict[hexRepOfOp(op)] = opstr(op)
twodict = {}
for op in twosies:
    twodict[hexRepOfOp(op)] = opstr(op)

# barf some temporaries just for reference later
outfile=open("onebyte_dict.json",'w')
json.dump(onedict,outfile)
outfile.close()

outfile=open("twobyte_dict.json",'w')
json.dump(twodict,outfile)
outfile.close()

# now transform source file --------------------------------------------------------------------------------

# - for weird exec counting function
caseline = re.compile("(                        case )(0x[0-9a-f]+):.*")
def strip_1(str):
    return str
onebyte_start = 3176
twobyte_start = 3177
twobyte_end = 3546

# - for normal instruction format: 0xXX
#caseline = re.compile("(\s+case )(0x[0-9a-f]+):.*")
#def strip_1(str):
#    return str
#onebyte_start = 5662
#twobyte_start = 7551
#twobyte_end = 8291

# - for 16bit compat instruction format: 0x1XX
#caseline = re.compile("(\s+case )(0x1[0-9a-f]+):.*")
#def strip_1(str):
#    return "0x"+str[-2:]
#onebyte_start = 8472
#twobyte_start = 9245
#twobyte_end = 9647

emulatorlines = open("cpux86-ta.js","r").readlines()
newlines=[]
for i,line in enumerate(emulatorlines):
    if i < onebyte_start:
        newlines.append(line)
    if onebyte_start <= i < twobyte_start: #one-byte instructions
        linematch=caseline.match(line)
        if linematch:
            try:
                newlines.append(linematch.group(1)+linematch.group(2)+"://"+onedict[strip_1(linematch.group(2))]+"\n")
            except KeyError:
                newlines.append(line)
        else:
            newlines.append(line)
    if twobyte_start <= i < twobyte_end: #two-byte instructions
        linematch=caseline.match(line)
        if linematch:
            try:
                newlines.append(linematch.group(1)+linematch.group(2)+"://"+twodict[strip_1(linematch.group(2))]+"\n")
            except KeyError:
                newlines.append(line)
        else:
            newlines.append(line)
    if twobyte_end <= i:
        newlines.append(line)

outfile=open("cpux86-ta-auto-annotated.js",'w')
outfile.writelines(newlines)
outfile.close()
