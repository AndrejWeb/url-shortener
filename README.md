# URL Shortener Demo

Web application for shortening URLs. Shortened URLs can be deleted but not edited. URL visits are also tracked (how many times URL is visited). In summary, this app is a good starting point if you want to develop full web app for shortening URLs. 

app/ - this is where app files are located. It is recommended this folder to be outside /public_html/ on your live server.

index.php - route file which handles URL redirection

admin.php - admin file where URLs are managed. It is highly recommended that this file is put behind login wall.

db.sql - SQL file/queries for database and tables creation

Database credentials are set in:
app/src/Database/DB.php

In order to run the project, make sure you have Composer installed and run the following command in project directory:

    composer install
    
If the project is not located in root directory of /public_html/ but in subfolder, you'll need to update RewriteBase in .htaccess. So if your project is hosted at www.example.com/url-shortener/ then RewriteBase should be
   
    RewriteBase /url-shortener/
    
This rule also applies to localhost (xampp or wamp). 

Last but not least in order the shorten URLs to work you need to enable Apache module __mod_rewrite__ if it is disabled.

Created by: https://andrejphp.is/

### Screenshots

1)
![Url](https://i.imgur.com/7XpiLG7.png)

2)
![Url](https://i.imgur.com/NSPVjcB.png)

3)
![Url](https://i.imgur.com/7uqj4eY.png)

4)
![Url](https://i.imgur.com/iaHniuS.png)