# Blog-Posting-Website-Network-Security
Final Project for Network Security Class offered at Salem State.
# Summary
For this term project, we were unable to utilize PHP sessions, and had to use cookies for authentication and user management. User sessions are managed through cookies, and the contents are signed using HMAC to ensure their authenticity. Once a user logs in, a cookie is generated as well as a random “session ID” for the user and their public IP4 address is recorded in the database to verify their session. The database connection credentials to the MySql database is encrypted using the AES-256 algorithm. The encrypted credentials, the key, and the IV used for all encryption and decryption are stored in a non-public folder in the linux host machine. User’s can register, login, post and comment on existing posts. If a website visitor is not logged in, they can post as “Anonymous”. All user input is sanitized for scripting tags and HTML characters before it is inserted into the database. For every database call, prepared statements are used.
