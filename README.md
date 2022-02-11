# Mikrotik-Capsman-WebUI

## BY: Arief Yudhawarman | awarmanf.wordpress.com
## MODIFIED BY: Valen Brata | github.com/Valthion

A simple improvement project over Arief Yudhawarman's Project. Credits to Arief Yudhawarman (awarmanf.wordpress.com).
The original project has deprecated syntaxes and API so it must be reworked almost completely while at the same time adding new features.
***These codes tested on RouterOS v6.44.6 - v6.48.6***

![WORKING](https://user-images.githubusercontent.com/65765848/153013685-f47b0720-f6e7-4104-80bd-115b6f2a56e1.png)
![WORKING_2](https://user-images.githubusercontent.com/65765848/153013716-33d0db18-f846-453e-9201-cf5a5c140a9f.png)

### FEATURES:
1. CHANGING SSID NAME (MASTER ONLY)
2. ADD AND CLEAR PASSWORD* (MASTER ONLY)
3. ADD AND UPDATE CLIENTS
4. BLOCK AND UNBLOCK CLIENTS
5. RESTART AP
6. CONFIGURE CHANNEL FREQUENCIES AND TX POWER
- *To clear password, just leave it blank. Security auth types will be automatically cleared.

### PREREQUISITES:
1. PHP v7
2. MikroTik Devices that supports WiFi and CapsMan
3. Web server with MySQL (XAMPP Recomemended). Database included but feel free to make one.

### HOW TO INSTALL:
1. Configure your CapsMan. Make sure your CapsMan master interface already set up including the RouterOS API and user access (Grant full access).
2. To set the channel frequency, make sure make a template for it. You can change the option in configCaps.php and index.php
3. Install web server.
4. Put these codes into a folder (ex. capsman) and put the folder in the htdocs folder.
5. Create a database with hostname table. Follow this:
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
```
EXAMPLE:
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'capsman');
```
6. Edit capsman.inc.php by entering your CapsMan IP, Gateway IP, API user and API password. If the CapsMan perform as the gateway as well, fill with same IP.
```
EXAMPLE:
DEFINE ('IP_CAPS', '192.168.214.100');
DEFINE ('IP_GW', '192.168.214.100');
DEFINE ('API_USR', 'usrapi');
DEFINE ('API_PASS', 'password');
```
7. Access it via your webserver (ex. http://web_server_ip/capsman/)
8. Enjoy!

### KNOWN ISSUES:
1. Channel should be chosen when setting the ap.
2. Empty ssid will return to default value you've set in winbox configurations. If nothing is set (textbox empty and no SSID in configurations), it will appears empty but the old SSID still present.
3. Many undefined index errors which occurs due to empty data/arrays but mostly it won't affect the app (ex. No slaves in CapsMan or no clients connected). Make sure to turn off your error display in php to avoid this.
![ERROR_1_NO_SLAVES](https://user-images.githubusercontent.com/65765848/153013578-38920021-43a4-487c-984c-72a2a3122561.png)
![ERROR_2_NO_CLIENT](https://user-images.githubusercontent.com/65765848/153013600-31713556-4ce1-4d6f-825c-07fe742b3bee.png)
