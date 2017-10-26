# PiPager
This is the Raspberry Pi part of the LeoPager project.<br />

Setup -<br />
1/ Install Raspberry Pi Noobs https://www.raspberrypi.org/downloads/noobs/ onto your Pi<br />
2/ Copy the contents of .zip to a USB fob and put the fob into the Pi<br />
3/ Follow the installation instructions in /software/SetupBash.txt to get the linux support app in place<br />
4/ Copy the files in /software/_Home_pi_code to the /home/pi directory<br />
5/ Copy the files in /software/_www_code to the www directory of you Pi<br />
6/ Use phpadmin to create the database from the /software/SQL_setup/VEX_TM_Pager.sql on the FOB<br />
7/ Edit the file Banks.txt on the FOB to reflect the bank IDs of the pagers you have. The supplied Banks.txt shows the codes for the 15 banks that app has been tested with. Note the three letter codes before the banks are used to colour code the banks with dots of nail polish. WWB is three dots white-white-blue. You can use any three letter combo you desire. This was done to make it easy to identify pagers when more than one bank is used.<br />

The three non 'software' directories on the FOB are the directories that contain the team and match lists exported from VEX Tournament manager. The TeamList.csv files have been modified to replace the sponsor with the pager ID that the team has for example GGG7 indicates that the team has pager 7 from bank GGG. This field can be filed in during the check in process and the files exported later.<br />


