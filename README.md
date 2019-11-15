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

# Backend choice

php backend is used by default. To switch on [Spring Boot backend][] set
`backEndURI = javaBackEndURI` and configure `javaBackEndURI.host` and
`javaBackEndURI.port` in `build/js/client.js`.

```JavaScript
javaBackEndURI.host = "http://localhost";
javaBackEndURI.port = "8080";
...
const backEndURI = javaBackEndURI; // php or java
```

# Run

`{project_directory}/build/index.html`

[Spring Boot backend]: https://github.com/Kirill-Bugaev/springboot-chat-backend
