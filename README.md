# Instagram-clone
Whole new Instagram web-app with features from Facebook, Twitter &amp; Google+. Believe it's much better than their Instagram!!!

[More screenshots](https://www.dropbox.com/sh/7yysaawc7fn4ls0/AAAebtBOyYk-hiLXBjHxTz-da?dl=0 "More screenshots")

# Own this project
Few works to do:
  1. Import SQL file.          
  2. Change user, password and host (can be done easily by CTRL+SHIFT+F).
  3. Change root path /faiyaz/Instagram to yourrootpath.
 
 Didn't get it. Below are extended details:
 
1. There's a SQL file named "instagram.sql" it contains all details database details.
2. Import the same SQL file in PHPMyAdmin and it will create the Instagram database.
3. Replace the username, password and host of PDO connection with yours by pressing CTRL+SHIFT+F (in Atom and VS Code for searching the whole project) or else App won't work.                                                                                                Press CTRL+SHIFT+F insert "$db = new PDO('mysql:host=host;dbname=instagram;charset=utf8mb4', 'user', 'password')" in search box         "$db = new PDO('mysql:host=YOUR_HOST;dbname=instagram;charset=utf8mb4', 'USER', 'PASSWORD');"
4. The root of this project is /faiyaz/Instagram/ and in your case the root will be different for eg. http://www.yoursite.com/. Search "/faiyaz/Instagram" and replace it with "yourroot". Replace easily by CTRL+SHIFT+F.
