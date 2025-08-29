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
- **Inventory** – Add cards, track quantities, and view where each card is used.  
- **Build a Deck** – Create decks from your inventory. Cards are deducted from inventory when placed into decks and can be returned later. Quantities can be split across multiple decks.  
- **Friends** – Add friends and view their inventories.  
- **Profile** – Choose an avatar. Avatars also display on the navigation bar and friends list.  
- **Help** – Usage instructions.  
- **Login/Register** – Simple accounts with plaintext passwords.

## Notes
- Ensure all dependencies are installed using `npm install` before running the
application.
-Dependency #1 -- XAMPP

## Link to Project
(https://github.com/NORHER6222/MagicTheGathering-Website/tree/main)
## License
This project is licensed under the GNU License - see the [LICENSE](LICENSE) file
for details.


