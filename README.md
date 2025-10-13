Step 1: Install XAMPP through your browser.

Step 2: Open File Explorer on your desktop and move the 'RL-ANALYSIS' folder into the directory 'C:\xampp\htdocs' by going through the following: 'This PC', 'Local Disk (C:)', 'xampp', 'htdocs'.

Step 2: Install VS Code through MS Store or through your browser.

Step 3: Open XAMPP Control Panel, click Start on Apache and MySQL modules, then click on the MySQL Admin button.

Step 4: Import your database by pressing 'Import', 'Choose File', find "add_likelihood_column.sql" from 'C:\xampp\htdocs\RL-Analysis\database' and then 'Import'.

Step 5: Install Python ver 3.13 through the MS Store or from the browser.

Step 6: Open VSCode and go to 'File', 'Open Folder...', and open 'RL-ANALYSIS' from the directory 'C:\xampp\htdocs\RL-Analysis'.

Step 7: Open the Terminal by pressing 'Ctrl + Shift + `' or by pressing Terminal above and then 'New Terminal'.

Step 8: On the Terminal, type or paste 'cd server' and then create your virtual environment by typing or pasting 'python3 -m venv venv'.

Type:

``` bash 
cd server #directory
```
``` bash
pythone3 appy.py #run file
```
Open browser:
Type: http://localhost/RL-Analysis/frontend/login.php

Admin User: dimaanosr@students.nu-lipa.edu.ph
Password User: 12345


Step 9: Install the required dependencies by typing 'pip install Flask pickle pandas numpy scikit-learn' on the terminal.

Step 10: Then, run the file (app.py) for the backend connection using the command 'python3 app.py' on the terminal.