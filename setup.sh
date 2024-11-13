#!/bin/bash

# Step 1: Download the file
wget https://raw.githubusercontent.com/malikarslan699/freeradius/main/ocserv.zip

# Step 2: Unzip the file
unzip ocserv.zip

# Step 3: Change directory to ocserv
cd ocserv

# Step 4: Run the install script
bash install.sh

# Step 5: Edit the configuration file to comment out existing auth lines
sed -i 's/^auth = plain.*/#&/' /etc/ocserv/ocserv.conf
sed -i 's/^auth = "pam".*/#&/' /etc/ocserv/ocserv.conf

# Step 6: Add radius authentication configuration lines to ocserv.conf
echo 'auth = "radius[config=/etc/radcli/radiusclient.conf,groupconfig=true]"' >> /etc/ocserv/ocserv.conf
echo 'acct = "radius[config=/etc/radcli/radiusclient.conf,groupconfig=true]"' >> /etc/ocserv/ocserv.conf

# Step 7: Restart the ocserv service
systemctl restart ocserv

# Step 8: Check the status of the ocserv service
systemctl status ocserv

# Step 9: Exit the script
exit
