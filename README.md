# Magic The Gathering Website

## Project Description
The **Magic The Gathering Website** is a PHP/MySQL web application that allows users to manage their Magic: The Gathering card collections, build decks, and connect with friends. The platform includes user authentication, inventory management, deck creation, a social friends feature, and profile customization with avatars.  

## Prerequisites
- PHP 7.x or later  
- MySQL  
- Apache (XAMPP recommended)  
- A modern browser (Chrome, Edge, Firefox, etc.)

## Quick Setup
1. **Copy Project Files**  
   Unzip the `mtg-website` folder into your web server’s document root.  
   - Example for XAMPP (Windows): `C:\xampp\htdocs\mtg-website\`

2. **Start Services**  
   - Start **Apache**  
   - Start **MySQL**

3. **Load Database**  
   - Open **phpMyAdmin** at [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
   - Click **Import**  
   - Choose `mtg_site_full_setup.sql`  
   - Click **Go**  
   > This will create the database `mtg_site` with all required tables, mock users, and sample cards.

4. **Log In**  
   Visit: [http://localhost/mtg-website/](http://localhost/mtg-website/)  

   Use demo account (username/password):  
   - user1 / user1    

---

## Features
- **Home** – Dashboard with a snapshot of inventory and friends.  
- **Inventory** – Add, search, and manage cards. View quantities and where each card is used.  
- **Build a Deck** – Create decks from inventory. Cards are deducted from inventory and can be returned.  
- **Friends** – Add friends and view their inventories through profile links.  
- **Profile** – Customize profile by selecting avatars, displayed in navigation and friend lists.  
- **Help** – Provides usage instructions.  
- **Login/Register** – Secure account system with demo accounts for quick testing.  

---

## Project Tasks
- **Task 1: Set up the development environment**
  - Install PHP, MySQL, Apache (via XAMPP)  
  - Configure project files in web root and initialize database  

- **Task 2: Design the application**
  - Navigation bar created for consistent user flow across pages  
  - Defined layout for inventory, friends, and profile sections  

- **Task 3: Develop the frontend**
  - HTML and PHP used to render user interfaces dynamically  
  - CSS for styling and user-friendly layout  

- **Task 4: Develop the backend**
  - PHP scripts for handling user login, inventory management, and friend requests  
  - MySQL used for relational data storage of users, cards, decks, and friendships  

- **Task 5: Implement authentication**
  - User login and registration system  
  - Google reCAPTCHA integrated into login for security  

- **Task 6: Connect to the database**
  - MySQL database (`mtg_site`) with tables for users, inventory, decks, and friends  
  - SQL import file for easy setup  

- **Task 7: Test the application**
  - Verified inventory CRUD functionality  
  - Tested deck creation and card assignment  
  - Checked friend search and inventory viewing  

- **Task 8: Deploy the application**
  - Designed for local deployment on XAMPP  
  - Can be adapted for hosting on cloud servers  

- **Task 9: Document the project**
  - README with setup instructions and features  
  - Notes on tasks, functionality, and usage  

---

## Project Skills Learned
- PHP and MySQL development for dynamic web apps  
- Database management and SQL integration  
- User authentication and security (Google reCAPTCHA)  
- Frontend and backend integration  
- Version control with GitHub  
- Iterative/agile development process  
- Writing technical documentation  

---

## Languages and Technologies Used
- **PHP**: Backend logic  
- **MySQL**: Database storage  
- **HTML/CSS**: Frontend structure and styling  
- **JavaScript**: Interactivity (AJAX for searches, etc.)  
- **Google reCAPTCHA**: Security integration  

---

## Development Process
- **Agile Methodology**: Iterative development with frequent testing and updates.  
- Incremental feature additions (inventory, deck building, friends, profile, etc.).  

---

## Notes
- Ensure XAMPP services (Apache, MySQL) are running before use.  
- Use `mtg_site_full_setup.sql` to initialize the database.  
- Demo account (user1) is available for testing.  


- Ensure all dependencies are installed using `npm install` before running the
application.
-Dependency #1 -- XAMPP

## Link to Project
(https://github.com/NORHER6222/MagicTheGathering-Website/tree/main)
## License
This project is licensed under the GNU License - see the [LICENSE](LICENSE) file
for details.
