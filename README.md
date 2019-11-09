# chat

Simple chat.

# Configuring MySQL

1. mysql -u root -p
2. CREATE DATABASE chat;
3. GRANT ALL PRIVILEGES ON chat.\* TO 'chat'@'localhost' IDENTIFIED BY 'chat';
4. quit;
5. mysql -u chat -p
6. USE chat;
7. SOURCE {project_directory}/sql/create_tables.sql;

