# Mikrotik-Capsman-WebUI

## BY: Arief Yudhawarman | awarmanf.wordpress.com
## MODIFIED BY: Valen Brata | github.com/Valthion

A simple improvement project over Arief Yudhawarman's Project. Credits to Arief Yudhawarman (awarmanf.wordpress.com)

### FEATURES:
1. CHANGING SSID NAME (MASTER ONLY)
2. ADD AND CLEAR PASSWORD (MASTER ONLY)
3. ADD AND UPDATE CLIENTS
4. BLOCK AND UNBLOCK CLIENTS
5. RESTART AP
6. CONFIGURE CHANNEL FREQUENCIES AND TX POWER

### PREREQUISITES:
1. PHP v7
2. MikroTik Devices that supports WiFi and CapsMan
3. Web server with MySQL (XAMPP Recomemended)

### HOW TO INSTALL:
1. Configure your CapsMan. Make sure your CapsMan master interface already set up including the RouterOS API and user access (Grant full access).
2. Install web server.
3. Put these codes into a folder (ex. capsman) and put the folder in the htdocs folder.
4. Create a database with hostname table. Follow this:
```
DROP TABLE IF EXISTS `hostname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hostname` (
  `mac` char(17) NOT NULL,
  `hostname` varchar(40) DEFAULT NULL,
  `user` varchar(40) DEFAULT 'Unidentified User',
  PRIMARY KEY (`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```
5. Edit mysql.inc.php by entering your MySQL username and password, then host and database name.
6. Edit capsman.inc.php by entering your CapsMan IP, Gateway IP, API user and API password. If the CapsMan perform as the gateway as well, fill with same IP.
7. Access it via your webserver (ex. http://web_server_ip/capsman/)
8. Enjoy!

### KNOWN ISSUES:
1. CHANNEL SHOULD BE CHOSEN WHEN SETTING THE AP. EMPTY SSID WILL RETURN TO DEFAULT VALUE YOU'VE SET IN WINBOX CONFIGURATIONS.
2. MANY UNDEFINED INDEX ERRORS WHICH OCCURS DUE TO EMPTY DATA/ARRAYS BUT MOSTLY IT WON'T AFFECT THE APP. MAKE SURE TO TURN OFF YOUR ERROR DISPLAY IN PHP TO AVOID THIS.
